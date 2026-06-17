<div>
    <div>
        <script src="https://unpkg.com/jsplumb@2.15.6/dist/js/jsplumb.min.js"></script>
        <script src="assets/html-to-image.min.js"></script>
        
        <!--script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script-->

        <style>
            #canvas {
                width: 100%;
                min-width: 600px;
                height: 80vh;
                background: #fff;
                position: relative;
                border: 1px solid #ccc;
                overflow: visible; /* jsPlumb рисует линии за пределы блока */
                touch-action: pan-x pan-y; /* разрешаем прокрутку пальцем */
            }
            @media (max-width: 767.98px) {
                #canvas {
                    height: 60vh;
                    min-width: 500px;
                }
            }
            .grid-page {
                background-image:   linear-gradient(to right, #e0e0e0 1px, transparent 1px),
                                    linear-gradient(to bottom, #e0e0e0 1px, transparent 1px) !important;
                background-size: 20px 20px !important;
                background-color: #fafafa !important;
            }
            .node {
                position: absolute;
                /*height: 120px;*/
                border: 1px solid #333;
                background: #ffffff;
                text-align: center;
                cursor: move;
                user-select: none;
                -webkit-user-select: none;
                touch-action: none; /* jsPlumb перехватывает touch-события для drag */
            }
            /* Подсказка при наведении (ПК) */
            .node:hover {
                border-color: #09a0a1;
                box-shadow: 0 0 0 2px rgba(9,160,161,0.25);
            }
            /* Мобильная иконка-подсказка "тапни для редактирования" */
            .node::after {
                content: '';
                display: none;
            }

            .node-name {
                position: absolute;
                top: 0px;
                left: 0px;
            }

            .node-lable {
                position: absolute;
                top: 0px;
                left: 50%;
                font-weight:bold;
                line-height: 8px;

                
                width: 50%;
                height: 28px;
                align-content: center;
            }

            @media (hover: none) and (pointer: coarse) {
                /* Только сенсорные экраны: показываем значок карандаша в углу */
                .node {
                    cursor: pointer;
                }
                .node::after {
                    content: '✏️';
                    display: block;
                    position: absolute;
                    top: 2px;
                    right: 4px;
                    font-size: 10px;
                    line-height: 1;
                    opacity: 0.6;
                }
            }
            
            .node img {
                height: 100%;
            }
            
            .node .label {
                font-size: 10px;
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
            .btn-moove-right {
                position: absolute;
                z-index: 1;
                left: 30px;
                top: 20px;
            }
            .btn-moove-left {
                position: absolute;
                z-index: 1;
                left: 0px;
                top: 20px;
            }
            .btn-moove-up {
                position: absolute;
                z-index: 1;
                left: 15px;
                top: 0px;
            }
            .btn-moove-down {
                position: absolute;
                z-index: 1;
                left: 15px;
                top: 40px;

            }
            .btn-moove-right:hover, 
            .btn-moove-left:hover,
            .btn-moove-up:hover,
            .btn-moove-down:hover {
                background-color: #6c757d;
            }

            .your-canvas-wrapper {
                cursor: not-allowed;
                pointer-events: none;
            }

        </style>

        <div wire:ignore class="container-fluid py-3">
            <div class="row g-2">
                {{-- ===== Боковая панель компонентов ===== --}}
                <div class="col-12 col-md-3 col-lg-2">

                    {{-- Подсказка для мобильных --}}
                    <div class="d-md-none alert alert-info py-2 px-3 mb-2 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Нажмите <strong>«Добавить»</strong> под иконкой, чтобы добавить узел на схему.
                        Для редактирования узла — нажмите на него на схеме.
                    </div>

                    <div id="components" class="mb-2">
                        <div class="accordion accordion-flush border rounded" id="accordionFlushNodes">
                            @forelse($groups as $group)
                            <div class="accordion-item" @key="group-{{$group['id']}}">
                                <h2 class="accordion-header" id="flush-heading-{{$group['id']}}">
                                    <button class="accordion-button collapsed py-2" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-{{$group['id']}}"
                                        aria-expanded="false"
                                        aria-controls="flush-collapse-{{$group['id']}}">
                                        <span class="fw-semibold">{{$group['name']}}</span>
                                    </button>
                                </h2>
                                <div id="flush-collapse-{{$group['id']}}"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="flush-heading-{{$group['id']}}"
                                    data-bs-parent="#accordionFlushNodes">
                                    <div class="accordion-body p-2">
                                    {{-- Узлы добавляются через JS renderComponents() --}}
                                    </div>
                                </div>
                            </div>
                            @empty
                                <p class="p-2 small text-muted mb-0">Нет узлов для конфигуратора</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary btn-sm" onclick="saveData()">
                            <i class="bi bi-floppy me-1"></i>Сохранить схему
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="loadDataBtn()">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Вернуть сохранение
                        </button>
                        <button class="btn btn-success btn-sm" onclick="nextPage()">
                            <i class="bi bi-arrow-right-circle me-1"></i>Далее
                        </button>
                    </div>
                </div>

                {{-- ===== Канвас схемы ===== --}}
                <div class="col-12 col-md-9 col-lg-10" id="canvas-wrapper">
                    {{-- Кнопки расширения канваса --}}
                    <div class="d-flex gap-1 mb-2 flex-wrap">
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomLeft()"  title="Уменьшить ширину"><i class="bi bi-dash-square"></i> ←→</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomRight()" title="Увеличить ширину"><i class="bi bi-plus-square"></i> ←→</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomUp()"    title="Уменьшить высоту"><i class="bi bi-dash-square"></i> ↑↓</button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomDown()"  title="Увеличить высоту"><i class="bi bi-plus-square"></i> ↑↓</button>
                    </div>

                    <div style="overflow:auto;">
                        <div id="canvas" class="grid-page"></div>
                    </div>
                    <div id="canvas2"></div>
                </div>
            </div>
        </div>
        
        <!-- окно для данных ЧРП -->
        <livewire:blocks.form-edit-modal-fr />
        <livewire:blocks.form-edit-modal-upp />
        <livewire:blocks.form-edit-modal-other />

        <!-- окно для данных кабеля -->
        <livewire:blocks.form-edit-modal-cable />


        <script>        
            document.addEventListener('livewire:initialized', () => {
                console.log('livewire:initialized');
                
                // создание компонента laravel
                component = Livewire.getByName('configuration.configuration')[0];
            
            


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

                //const nodeSettings =  JSON.parse(`{!! $node !!}`);
                const nodeSettings = JSON.parse(@json($node));

                // Хранение текущей схемы: узлы и соединения
                let savedSchema = {
                    nodes: [],
                    connections: [],
                    page: {
                            width: 0,
                            height: 0,
                        },
                };
                savedSchema = component.get('saved_schema');

                
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
                let modalId = null;

                /**
                 * Вспомогательная функция: определяет одиночный тап на сенсорном экране.
                 * Срабатывает если палец не сдвинулся больше 10px и время касания < 400ms.
                 * Используется как мобильный аналог dblclick на узлах схемы.
                 */
                function addTapHandler(el, callback) {
                    let _tx = 0, _ty = 0, _tt = 0, _tapTimer = null;
                    el.addEventListener('touchstart', e => {
                        _tx = e.touches[0].clientX;
                        _ty = e.touches[0].clientY;
                        _tt = Date.now();
                        // При первом тапе запускаем таймер ожидания второго
                        if (_tapTimer) {
                            clearTimeout(_tapTimer);
                            _tapTimer = null;
                        }
                    }, { passive: true });
                    el.addEventListener('touchend', e => {
                        if (!e.changedTouches.length) return;
                        const dx = Math.abs(e.changedTouches[0].clientX - _tx);
                        const dy = Math.abs(e.changedTouches[0].clientY - _ty);
                        const dt = Date.now() - _tt;
                        // Считаем тапом: нет смещения И короткое нажатие
                        if (dx < 10 && dy < 10 && dt < 400) {
                            e.preventDefault();
                            callback(e);
                        }
                    });
                }

                // Рендер списка компонентов слева
                function renderComponents() {

                    canvas.style.width = savedSchema.page.width + 'px';
                    canvas.style.height = savedSchema.page.height + 'px';

                    const container = document.getElementById("components");
                    //container.innerHTML = "";
                    nodeSettings.forEach(item => {
                        //console.log(item);
                        const el = document.createElement("div");
                        el.className = "mb-2 border rounded p-2 text-center";
                        el.setAttribute("draggable", "true");
                        el.setAttribute("data-type", item.type);
                        el.setAttribute("data-id", item.template.id);
                        el.innerHTML = `
                            <img src="${item.image}" alt="${item.name}" style="width:44px;height:44px;object-fit:contain;">
                            <div class="small mt-1 fw-semibold">${item.name}</div>
                            <button class="btn btn-outline-success btn-sm mt-1 w-100 node-add-btn" type="button"
                                title="Добавить на схему (перетащите на ПК или нажмите на мобильном)">
                                <i class="bi bi-plus-lg me-1"></i>Добавить
                            </button>`;
                        // ПК: drag-and-drop
                        el.addEventListener("dragstart", e => {
                            e.dataTransfer.setData("type", item.type);
                        });
                        // Мобильные и ПК: клик по кнопке «Добавить» → размещаем узел на координатах 0,0
                        el.querySelector('.node-add-btn').addEventListener('click', e => {
                            e.stopPropagation(); // не всплываем до accordion-кнопки
                            createNode(item.type, 0, 0);
                            positionUpdate();
                        });
                        // Добавляем внутрь .accordion-body (если есть), иначе прямо в collapse-div
                        const collapseDiv = document.getElementById("flush-collapse-"+item.node_group.id);
                        const bodyDiv = collapseDiv ? collapseDiv.querySelector('.accordion-body') : null;
                        (bodyDiv || collapseDiv).appendChild(el);
                    });
                }
                // Обработка перетаскивания компонентов на канвас
                canvas.addEventListener("dragover", e => e.preventDefault());
                canvas.addEventListener("drop", e => {
                    e.preventDefault();
                    const type = e.dataTransfer.getData("type");
                    createNode(type, e.offsetX, e.offsetY);
                    positionUpdate();
                });
                // Создание нового узла
                function createNode(type, x, y, savedId = null, savedName = "", savedExtra = "", n = null) {
                    const settings = nodeSettings.find(n => n.type === type);
                    if (!settings) return;
                    const node = document.createElement("div");
                    const id = savedId || "node" + Date.now(); //nodeIdCounter++;
                    const node_group_id = settings.node_group.id;
                    const template_id = settings.template_id;
                    const name = settings.name;
                    node.className = n?.product?.id ? "node" : "node bg-danger";
                    node.title = "Нет привязки к продукту";
                    node.id = id;
                    node.style.left = x + "px";
                    node.style.top = y + "px";
                    const img = document.createElement("img");
                    img.src = settings.image;
                    
                    const labelName = savedName || settings.defaultName;
                    const labelExtra = savedExtra || settings.defaultExtra;
                    node.innerHTML += `<!--div class="node-name">${settings.name}</div--><div class="label node-lable"><div>${labelName}${labelExtra ? '<br/>'+labelExtra : ""}</div></div>`;
                    node.appendChild(img);

                    node.dataset.name = labelName;
                    node.dataset.extra = labelExtra;                
                    node.dataset.group_id = node_group_id;
                    node.dataset.template_id = template_id;
                    
                    // для ЧРП отельное окно. 1 = группа ЧРП, остальные - для остальных продуктов. В дальнейшем можно будет расширить
                    if (template_id == 1) {
                        // ПК: двойной клик / Мобильный: одиночный тап
                        const openModalFR = () => {
                            modalTarget = node;
                            modalType = "node";
                            modalId = id;
                            document.getElementById("modal-input1-fr").value = node.dataset.name;
                            document.getElementById("modal-input2-fr").value = node.dataset.extra;
                            document.getElementById("modal-title-node-fr").innerText = "Редактировать " + name;
                            Livewire.dispatch('updateFilter', { template_id: settings.template.id, node_id: id });
                            const modal = new window.bootstrap.Modal(document.getElementById('editModalFR'));
                            modal.show();
                        };
                        node.addEventListener("dblclick", openModalFR);
                        addTapHandler(node, openModalFR);
                    // окно для UPP
                    } else if (template_id == 4) {
                        // ПК: двойной клик / Мобильный: одиночный тап
                        const openModalUPP = () => {
                            modalTarget = node;
                            modalType = "node";
                            modalId = id;
                            document.getElementById("modal-input1-upp").value = node.dataset.name;
                            document.getElementById("modal-input2-upp").value = node.dataset.extra;
                            document.getElementById("modal-title-node-upp").innerText = "Редактировать " + name;
                            Livewire.dispatch('updateFilter', { template_id: settings.template.id, node_id: id });
                            const modal = new window.bootstrap.Modal(document.getElementById('editModalUPP'));
                            modal.show();
                        };
                        node.addEventListener("dblclick", openModalUPP);
                        addTapHandler(node, openModalUPP);
                    // окно для остальных продуктов
                    } else {
                        // ПК: двойной клик / Мобильный: одиночный тап
                        const openModalNode = () => {
                            modalTarget = node;
                            modalType = "node";
                            modalId = id;
                            document.getElementById("modal-input1").value = node.dataset.name;
                            document.getElementById("modal-input2").value = node.dataset.extra;
                            document.getElementById("modal-title-node").innerText = "Редактировать " + name;
                            Livewire.dispatch('updateFilter', { template_id: settings.template.id, node_id: id });
                            const modal = new window.bootstrap.Modal(document.getElementById('editModal'));
                            modal.show();
                        };
                        node.addEventListener("dblclick", openModalNode);
                        addTapHandler(node, openModalNode);
                    }

                    canvas.appendChild(node);
                    // Делаем узел перетаскиваемым
                    instance.draggable(node, {
                        grid: [20, 20],
                        stop: () => {
                            const n = savedSchema.nodes.find(n => n.id === node.id);
                            if (n) {
                                n.x = parseInt(node.style.left);
                                n.y = parseInt(node.style.top);
                            }
                            positionUpdate();
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
                            maxConnections: 100
                        });
                    });
                    if (!savedSchema.nodes.find(n => n.id === id)) {
                        savedSchema.nodes.push({
                            id,
                            type,
                            x,
                            y,
                            name: labelName,
                            extra: labelExtra,
                            product_id: 0,
                            template_id: settings.template.id,
                            filter_fields: [],
                            rules_fields: [],
                            //count: 1,
                        });

                    }
                }
                // Обработка создания соединения
                instance.bind("connection", info => {
                    
                    const conn = info.connection;
                    const sourceUUID = conn.endpoints[0].getUuid();
                    const targetUUID = conn.endpoints[1].getUuid();
                    const conn_id = conn.sourceId + '-' + conn.targetId;
                    const template_id = 0;
                    const exists = savedSchema.connections.some(c => c.sourceEndpoint === sourceUUID && c.targetEndpoint === targetUUID);
                    if (!exists) {
                        savedSchema.connections.push({
                            
                            source: conn.sourceId,
                            target: conn.targetId,
                            sourceEndpoint: sourceUUID,
                            targetEndpoint: targetUUID,
                            params: {
                                type: '',
                                length: '1',
                                filter_fields: [],
                                rules_fields: [],
                                template_id: template_id,
                                id: conn_id,
                                product_id: 0,
                            },
                            
                        });

                        positionUpdate();
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
                        document.getElementById("modal-input10").value = currentParams.type || "";
                        document.getElementById("modal-input11").value = currentParams.length || "";
                        document.getElementById("modal-title-conn").innerText = "Редактировать соединение";

                        Livewire.dispatch('updateFilter', { template_id: template_id, conn_id: conn_id });

                        const modal = new bootstrap.Modal(document.getElementById('editModalCable'));
                        modal.show();
                    });

                });
                // Сохранение изменений модального окна
                function saveModal() {
                    
                    if (modalType === "node" && modalTarget) {
                        const group_id = modalTarget.dataset.group_id;
                        const template_id = modalTarget.dataset.template_id;
                        let val1 = "";
                        let val2 = "";

                        // для чрп получаем значение доп полей
                        if (template_id == 1) {
                            val1 = document.getElementById("modal-input1-fr").value;
                            val2 = document.getElementById("modal-input2-fr").value;
                        }
                        // для упп получаем значение доп полей
                        else if (template_id == 4) {
                            val1 = document.getElementById("modal-input1-upp").value;
                            val2 = document.getElementById("modal-input2-upp").value;
                        }
                        // для остальных продуктов получаем значение доп полей
                        else {
                            val1 = document.getElementById("modal-input1").value;
                            val2 = document.getElementById("modal-input2").value;
                        }

                        modalTarget.dataset.name = val1;
                        modalTarget.dataset.extra = val2;
                        const label = modalTarget.querySelector(".label");
                        if (label) label.innerHTML = '<div>'+val1 + (val2 ? '<br/>' + `${val2}` : "")+'</div>';
                        const nodeInSchema = savedSchema.nodes.find(n => n.id === modalTarget.id);
                        if (nodeInSchema) {
                            nodeInSchema.name = val1;
                            nodeInSchema.extra = val2;
                        }
                        
                        Livewire.dispatch('searchProduct', { node_id: modalTarget.id, type: 'nodes' });
                    }
                    if (modalType === "connection" && modalTarget) {
                        const val1 = document.getElementById("modal-input10").value;
                        const val2 = document.getElementById("modal-input11").value;

                        modalTarget.setParameter("params", {
                            type: val1,
                            length: val2,
                            id: val2,
                        });
                        const overlay = modalTarget.getOverlay("label");
                        if (overlay) overlay.setLabel(val1 + (val2 ? ` (${val2}м)` : ""));
                        const index = savedSchema.connections.findIndex(c => c.source === modalTarget.sourceId && c.target === modalTarget.targetId);
                        



                        if (index !== -1) {
                            const paramsConn = savedSchema.connections[index].params;

                            paramsConn.type = val1;
                            paramsConn.length = val2;
                            modalTarget.setParameter("params", paramsConn);
                            
                            /*savedSchema.connections[index].params = {
                                type: val1,
                                length: val2
                            };*/

                            Livewire.dispatch('searchProduct', { conn_id: paramsConn.id, type: 'connections' });
                        }
                        
                        
                    };

                    // посик продукта
                    
                }
                // Удаление узла или соединения
                function deleteModalTarget() {
                    // Проверяем, удаляем ли мы соединение (а не узел) и что modalTarget задан
                    if (modalType === "connection" && modalTarget) {
                        try {
                            // Получаем UUID точек подключения (источника и приёмника) у соединения
                            const sourceUUID = modalTarget.endpoints[0].getUuid();
                            const targetUUID = modalTarget.endpoints[1].getUuid();

                            // Удаляем визуальное соединение с canvas (из jsPlumb)
                            instance.deleteConnection(modalTarget);

                            // Удаляем соединение из savedSchema.connections
                            // Учитываем оба направления (A → B или B → A), чтобы точно удалить
                            savedSchema.connections = savedSchema.connections.filter(c => {
                                const forward = c.sourceEndpoint === sourceUUID && c.targetEndpoint === targetUUID;
                                const reverse = c.sourceEndpoint === targetUUID && c.targetEndpoint === sourceUUID;
                                return !(forward || reverse); // Удаляем, если найдено в любом направлении
                            });
                            
                             console.log(modalTarget);

                            // Отправляем событие Livewire, что элемент удалён (по ID соединения)
                            Livewire.dispatch('deleteProduct', { id: modalTarget.id });

                            // Обновляем Livewire-состояние на сервере (передаём актуальную схему)
                            component.set('saved_schema', savedSchema);

                        } catch (err) {
                            // Если произошла ошибка при удалении соединения — логируем её
                            console.error("Ошибка при удалении соединения:", err);
                        }
                    }

                    // Если удаляем узел
                    if (modalType === "node" && modalTarget) {
                        // Удаляем визуально узел с canvas
                        instance.remove(modalTarget.id);

                        // Удаляем узел из savedSchema.nodes
                        savedSchema.nodes = savedSchema.nodes.filter(n => n.id !== modalTarget.id);

                        // Также удаляем все соединения, где этот узел был источником или приёмником
                        savedSchema.connections = savedSchema.connections.filter(c => c.source !== modalTarget.id && c.target !== modalTarget.id);

                        // Отправляем сигнал Livewire, что продукт удалён (по ID узла)
                        Livewire.dispatch('deleteProduct', { id: modalTarget.id });
                        
                        // Обновляем Livewire-состояние (обновлённую схему)
                        component.set('saved_schema', savedSchema);

                    }
                }
                // Сохранить схему (пока только в консоль)
                function saveData() {
                    component.set('saved_schema', savedSchema);
                    // Отправляем сигнал Livewire, что продукт удалён (по ID узла)
                    Livewire.dispatch('saveBtn');
                    
                }
                // обновление после перемещения
                function positionUpdate() {
                    component.set('saved_schema', savedSchema);
                    
                }
                // для теста, кнопка проверки данных с базы
                function chekData() {
                    console.log(JSON.parse(JSON.stringify(component.get('saved_schema'))));
                }
                // запрос на обновление данных с базы
                function loadDataBtn() {
                    Livewire.dispatch('loadDataBtn');
                }
                // получение ответа, что данные готовы
                Livewire.on('finish-load-data', () => {
                    loadData();
                });
                // Загрузка схемы из savedSchema
                function loadData() {
                    savedSchema = component.get('saved_schema');

                    instance.deleteEveryConnection();
                    instance.deleteEveryEndpoint();
                    [...canvas.querySelectorAll(".node")].forEach(n => n.remove());

                    savedSchema.nodes.forEach(n => {
                        createNode(n.type, n.x, n.y, n.id, n.name, n.extra, n);
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
                        instance.repaintEverything();
                    }, 300);
                }
                // Сохранить канвас как изображение
                // не используется, осталось для теста
                function saveAsImage() {
                    
                    // скрывать пустые точки подключения перед созданием изображения
                    instance.selectEndpoints({}).each(ep => {
                        if (ep.connections.length === 0 && ep.canvas) {
                            ep.canvas.style.display = 'none';
                            //console.log(ep);
                        }
                    });

                    htmlToImage
                        .toJpeg(document.getElementById('canvas'), {
                            quality: 1,
                            pixelRatio: 3
                        })
                        .then(function(dataUrl) {
                            
                            //скачать файл локально 
                            var link = document.createElement('a');
                            link.download = 'my-image-name.jpeg';
                            link.href = dataUrl;
                            link.click();

                            // открывать пустые точки подключения после созданием изображения
                            instance.selectEndpoints({}).each(ep => {
                                if (ep.connections.length === 0 && ep.canvas) {
                                    ep.canvas.style.display = 'block';
                                }
                            });
                        }).catch(error => {
                            console.error("Caught error:", error.message || error);
                        });
                }

                // продолжить оформление ткп с сохранением изображения на сервере
                function nextPage() {
                    
                    // скрывать пустые точки подключения перед созданием изображения
                    instance.selectEndpoints({}).each(ep => {
                        if (ep.connections.length === 0 && ep.canvas) {
                            ep.canvas.style.display = 'none';
                            //console.log(ep);
                        }
                    });

                    document.getElementById('canvas').classList.remove('grid-page');

                    htmlToImage
                        .toJpeg(document.getElementById('canvas'), {
                            quality: 1,
                            pixelRatio: 3
                        })
                        .then(function(dataUrl) {
                            // загрузить на сервер
                            const base64 = dataUrl.replace(/^data:image\/jpeg;base64,/, '');
                            const filename = 'canvas-' + Date.now() + '.jpeg';

                            window.Livewire.dispatch('uploadImage', {
                                base64: base64,
                                //filename: filename
                            });

                            // открывать пустые точки подключения после созданием изображения
                            instance.selectEndpoints({}).each(ep => {
                                if (ep.connections.length === 0 && ep.canvas) {
                                    ep.canvas.style.display = 'block';
                                }
                            });

                            document.getElementById('canvas').classList.add('grid-page');
                        }).catch(error => {
                            console.error("Caught error:", error.message || error);
                        });
                }
                




                function zoomRight() {
                    if (!savedSchema.page.width) savedSchema.page.width = canvas.offsetWidth;
                    savedSchema.page.width += 100;
                    canvas.style.width = savedSchema.page.width + 'px';
                }
                function zoomLeft() {
                    if (!savedSchema.page.width) savedSchema.page.width = canvas.offsetWidth;
                    savedSchema.page.width = Math.max(300, savedSchema.page.width - 100);
                    canvas.style.width = savedSchema.page.width + 'px';
                }

                function zoomUp() {
                    if (!savedSchema.page.height) savedSchema.page.height = canvas.offsetHeight;
                    savedSchema.page.height = Math.max(200, savedSchema.page.height - 100);
                    canvas.style.height = savedSchema.page.height + 'px';
                }
                function zoomDown() {
                    if (!savedSchema.page.height) savedSchema.page.height = canvas.offsetHeight;
                    savedSchema.page.height += 100;
                    canvas.style.height = savedSchema.page.height + 'px';
                }

                Livewire.on('saved_schema-updated', () => {
                    //savedSchema = JSON.parse(JSON.stringify(component.get('saved_schema')));
                    savedSchema = component.get('saved_schema');
                    console.log('Данные saved_schema обновлены!',savedSchema);
                    updateColor ();
                });

                function updateColor (){
                    savedSchema.nodes.forEach(c => {
                        if (c.product_id > 0 || (c.template_id != 1 &&  c.template_id != 4)){
                            document.getElementById(c.id)?.classList.remove('bg-danger');
                            document.getElementById(c.id)?.setAttribute('title', c.product_name);
                        }
                        
                    }); 

                    /*modalTarget.setPaintStyle({
                        stroke: "#ff0000", // длинный кабель — красный
                        strokeWidth: 3
                    });*/
                }

                window.zoomUp = zoomUp;
                window.zoomDown = zoomDown;
                window.zoomLeft = zoomLeft;
                window.zoomRight = zoomRight;
                window.saveAsImage = saveAsImage;
                window.loadData = loadData;
                window.loadDataBtn = loadDataBtn;
                window.saveData = saveData;
                window.nextPage = nextPage;
                window.chekData = chekData;
                window.deleteModalTarget = deleteModalTarget;
                window.saveModal = saveModal;
                window.savedSchema = savedSchema;
                window.component = component;

                renderComponents();

                
                
                if(savedSchema.nodes)
                {
                    loadData();
                    updateColor();
                }
                
                

            });

            // инициализация
            document.addEventListener('livewire:updated', () => {
                console.log('livewire:updated!');
            });

            
        </script>

        
    </div>
</div>
