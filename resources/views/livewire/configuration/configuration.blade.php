<div>
    <div>


        <script src="https://unpkg.com/jsplumb@2.15.6/dist/js/jsplumb.min.js"></script>
        <script src="assets/html-to-image.min.js"></script>
        <!--script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script-->

        <style>
            #canvas {
                width: 100%;
                height: 80vh;
                background: #fff;
                position: relative;
                border: 1px solid #ccc;
                overflow: auto;
            }
            
            .node {
                position: absolute;
                width: 120px;
                height: 120px;
                border: 1px solid #333;
                background: #e3f2fd;
                text-align: center;
                padding-top: 5px;
                cursor: move;
            }
            
            .node img {
                width: 50px;
                height: 50px;
            }
            
            .node .label {
                font-size: 12px;
            }
            
            .connection-label {
                background: #fff;
                padding: 2px 4px;
                border: 1px solid #ccc;
                border-radius: 3px;
                font-size: 10px;
            }
            
            #canvas-wrapper {
                position: relative;
                overflow: visible;
            }
        </style>

        <div class="container-fluid py-3">
            <div class="row">
                <div class="col-md-2">
                    <div id="components" class="mb-3 text-center">

                        <div class="accordion accordion-flush" id="accordionFlushNodes">
                            @forelse($groups as $group)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-heading-{{$group['id']}}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse-{{$group['id']}}" aria-expanded="false" aria-controls="flush-collapse-{{$group['id']}}">
                                        {{$group['name']}}
                                    </button>
                                </h2>
                                <div id="flush-collapse-{{$group['id']}}" class="accordion-collapse collapse" aria-labelledby="flush-heading-{{$group['id']}}" data-bs-parent="#accordionFlushNodes">
                                <!-- Список узлов -->
                                </div>
                            </div>
                            @empty
                                <p>Нет узлов для конфигуратора</p>
                            @endforelse
                        </div>
                        
                    </div>
                    <button class="btn btn-secondary w-100 mb-2" onclick="saveAsImage()">Сохранить как изображение</button>
                    <button class="btn btn-secondary w-100 mb-2" onclick="saveData()">Сохранить схему</button>
                    <button class="btn btn-secondary w-100" onclick="loadData()">Загрузить схему</button>
                </div>
                <div class="col-md-10" id="canvas-wrapper">
                    <div id="canvas"></div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="modal-input1" class="form-control mb-2" placeholder="Название или тип">
                        <input type="text" id="modal-input2" class="form-control mb-2" placeholder="Дополнительно">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal">Удалить</button>
                        <button type="button" class="btn btn-primary" onclick="saveModal()" data-bs-dismiss="modal">Сохранить</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </div>
        </div>

        
        <script>
            // Конфигурация всех доступных типов узлов
            const _nodeSettings = [{
                type: "resistor",
                name: "Резистор",
                image: "https://cdn-icons-png.flaticon.com/128/484/484662.png",
                // Уникальные точки подключения для резистора
                endpoints: [{
                        anchor: [0.2, 1.1, 0, 1],
                        isSource: true,
                        isTarget: false
                    }, // выход снизу
                    {
                        anchor: [0.2, -0.1, 0, -1],
                        isSource: false,
                        isTarget: true
                    }, // вход сверху
                    {
                        anchor: [0.8, -0.1, 0, -1],
                        isSource: true,
                        isTarget: true
                    } // универсальная точка сверху
                ],
                defaultName: "Резистор",
                defaultExtra: ""
            }, {
                type: "lamp",
                name: "Лампа",
                image: "https://cdn-icons-png.flaticon.com/128/5228/5228860.png",
                endpoints: [{
                        anchor: [0.5, -0.1, 0, -1],
                        isSource: false,
                        isTarget: true
                    } // вход сверху
                ],
                defaultName: "Лампа",
                defaultExtra: ""
            }];
            const nodeSettings =  JSON.parse(`{!! $node !!}`);
            // Хранение текущей схемы: узлы и соединения
            let savedSchema = {
                nodes: [],
                connections: []
            };
            const canvas = document.getElementById("canvas");
            // Настройка jsPlumb инстанса
            const instance = jsPlumb.getInstance({
                Connector: ["Flowchart", {
                    cornerRadius: 15,
                    stub: [20, 20]
                }], // линии с отступом и скруглением
                PaintStyle: {
                    stroke: "#0077b6",
                    strokeWidth: 2
                }, // цвет и толщина линии
                Endpoint: ["Dot", {
                    radius: 5
                }], // стиль точки подключения
                EndpointStyle: {
                    fill: "#0077b6"
                }, // цвет точки
                Anchors: ["Top", "Bottom"] // дефолтные якоря
            });
            instance.setContainer(canvas); // Контейнер для jsPlumb
            let nodeIdCounter = 0;
            let modalTarget = null;
            let modalType = null;
            // Рендер списка компонентов слева
            function renderComponents() {
                const container = document.getElementById("components");
                //container.innerHTML = "";
                nodeSettings.forEach(item => {
                    const el = document.createElement("div");
                    el.className = "mb-2 border p-2 text-center";
                    el.setAttribute("draggable", "true");
                    el.setAttribute("data-type", item.type);
                    el.innerHTML = `<img src="${item.image}" alt=""><div>${item.name}</div>`;
                    el.addEventListener("dragstart", e => {
                        e.dataTransfer.setData("type", item.type);
                    });
                    

                    document.getElementById("flush-collapse-"+item.component_group.id).appendChild(el);

                    //container.appendChild(el);
                });
            }
            // Обработка перетаскивания компонентов на канвас
            canvas.addEventListener("dragover", e => e.preventDefault());
            canvas.addEventListener("drop", e => {
                e.preventDefault();
                const type = e.dataTransfer.getData("type");
                createNode(type, e.offsetX, e.offsetY);
            });
            // Создание нового узла
            function createNode(type, x, y, savedId = null, savedName = "", savedExtra = "") {
                const settings = nodeSettings.find(n => n.type === type);
                if (!settings) return;
                const node = document.createElement("div");
                const id = savedId || "node" + nodeIdCounter++;
                node.className = "node";
                node.id = id;
                node.style.left = x + "px";
                node.style.top = y + "px";
                const img = document.createElement("img");
                img.src = settings.image;
                node.appendChild(img);
                const labelName = savedName || settings.defaultName;
                const labelExtra = savedExtra || settings.defaultExtra;
                node.innerHTML += `<div>${settings.name}</div><div class="label">${labelName}${labelExtra ? ` (${
                    labelExtra
                })
                ` : ''}</div>`;
                node.dataset.name = labelName;
                node.dataset.extra = labelExtra;
                node.addEventListener("dblclick", () => {
                    modalTarget = node;
                    modalType = "node";
                    document.getElementById("modal-input1").value = node.dataset.name;
                    document.getElementById("modal-input2").value = node.dataset.extra;
                    document.getElementById("modal-title").innerText = "Редактировать узел";
                    const modal = new window.bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                });
                canvas.appendChild(node);
                // Делаем узел перетаскиваемым
                instance.draggable(node, {
                    stop: () => {
                        const n = savedSchema.nodes.find(n => n.id === node.id);
                        if (n) {
                            n.x = parseInt(node.style.left);
                            n.y = parseInt(node.style.top);
                        }
                    }
                });
                // Добавляем все точки подключения с уникальными UUID
                settings.endpoints.forEach((ep, index) => {
                    const endpointUUID = `${id}-ep-${index}`;
                    instance.addEndpoint(id, {
                        anchor: ep.anchor,
                        uuid: endpointUUID,
                        isSource: ep.isSource,
                        isTarget: ep.isTarget,
                        maxConnections: 1
                    });
                });
                if (!savedSchema.nodes.find(n => n.id === id)) {
                    savedSchema.nodes.push({
                        id,
                        type,
                        x,
                        y,
                        name: labelName,
                        extra: labelExtra
                    });
                }
            }
            // Обработка создания соединения
            instance.bind("connection", info => {
                const conn = info.connection;
                const sourceUUID = conn.endpoints[0].getUuid();
                const targetUUID = conn.endpoints[1].getUuid();
                const exists = savedSchema.connections.some(c => c.sourceEndpoint === sourceUUID && c.targetEndpoint === targetUUID);
                if (!exists) {
                    savedSchema.connections.push({
                        source: conn.sourceId,
                        target: conn.targetId,
                        sourceEndpoint: sourceUUID,
                        targetEndpoint: targetUUID,
                        params: {
                            type: '',
                            length: ''
                        }
                    });
                }
                const defaultLabel = 'Соединение';
                let existingOverlay = conn.getOverlay("label");
                if (!existingOverlay) {
                    conn.addOverlay(["Label", {
                        label: defaultLabel,
                        id: "label",
                        cssClass: "connection-label",
                        location: 0.5
                    }]);
                }
                conn.bind("dblclick", () => {
                    modalTarget = conn;
                    modalType = "connection";
                    const currentParams = conn.getParameter("params") || {};
                    document.getElementById("modal-input1").value = currentParams.type || "";
                    document.getElementById("modal-input2").value = currentParams.length || "";
                    document.getElementById("modal-title").innerText = "Редактировать соединение";
                    const modal = new bootstrap.Modal(document.getElementById('editModal'));
                    modal.show();
                });
            });
            // Сохранение изменений модального окна
            function saveModal() {
                const val1 = document.getElementById("modal-input1").value;
                const val2 = document.getElementById("modal-input2").value;
                if (modalType === "node" && modalTarget) {
                    modalTarget.dataset.name = val1;
                    modalTarget.dataset.extra = val2;
                    const label = modalTarget.querySelector(".label");
                    if (label) label.innerText = val1 + (val2 ? ` (${val2})` : "");
                    const nodeInSchema = savedSchema.nodes.find(n => n.id === modalTarget.id);
                    if (nodeInSchema) {
                        nodeInSchema.name = val1;
                        nodeInSchema.extra = val2;
                    }
                }
                if (modalType === "connection" && modalTarget) {
                    modalTarget.setParameter("params", {
                        type: val1,
                        length: val2
                    });
                    const overlay = modalTarget.getOverlay("label");
                    if (overlay) overlay.setLabel(val1 + (val2 ? ` (${val2})` : ""));
                    const index = savedSchema.connections.findIndex(c => c.source === modalTarget.sourceId && c.target === modalTarget.targetId);
                    if (index !== -1) {
                        savedSchema.connections[index].params = {
                            type: val1,
                            length: val2
                        };
                    }
                }
            }
            // Удаление узла или соединения
            function deleteModalTarget() {
                if (modalType === "node" && modalTarget) {
                    instance.remove(modalTarget.id);
                    savedSchema.nodes = savedSchema.nodes.filter(n => n.id !== modalTarget.id);
                    savedSchema.connections = savedSchema.connections.filter(c => c.source !== modalTarget.id && c.target !== modalTarget.id);
                }
                if (modalType === "connection" && modalTarget) {
                    instance.deleteConnection(modalTarget);
                    savedSchema.connections = savedSchema.connections.filter(c => !(c.sourceEndpoint === modalTarget.endpoints[0].getUuid() && c.targetEndpoint === modalTarget.endpoints[1].getUuid()));
                }
            }
            // Сохранить схему (пока только в консоль)
            function saveData() {
                console.log("Текущая схема:", JSON.stringify(savedSchema, null, 2));
            }
            // Загрузка схемы из savedSchema
            function loadData() {
                instance.deleteEveryConnection();
                instance.deleteEveryEndpoint();
                [...canvas.querySelectorAll(".node")].forEach(n => n.remove());
                savedSchema.nodes.forEach(n => {
                    createNode(n.type, n.x, n.y, n.id, n.name, n.extra);
                });
                setTimeout(() => {
                    savedSchema.connections.forEach(c => {
                        const conn = instance.connect({
                            uuids: [c.sourceEndpoint, c.targetEndpoint]
                        });
                        if (conn) {
                            conn.setParameter("params", c.params);
                            const label = `${c.params.type || ''}${c.params.length ? ' (' + c.params.length + ')' : ''}`;
                            const existingLabel = conn.getOverlay("label");
                            if (existingLabel) existingLabel.setLabel(label);
                        }
                    });
                }, 100);
            }
            // Сохранить канвас как изображение
            function saveAsImage() {
                htmlToImage
                    .toJpeg(document.getElementById('canvas'), {
                        quality: 0.95
                    })
                    .then(function(dataUrl) {
                        var link = document.createElement('a');
                        link.download = 'my-image-name.jpeg';
                        link.href = dataUrl;
                        link.click();
                    });
            }
            document.addEventListener('DOMContentLoaded', renderComponents);
        </script>


    </div>

</div>