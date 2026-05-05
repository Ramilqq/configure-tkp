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
            <div class="row">
                <div class="col-md-2">
                    <div id="components" class="mb-3 text-center">

                        <div class="accordion accordion-flush" id="accordionFlushNodes">
                            @forelse($groups as $group)
                            <div class="accordion-item" @key="group-{{$group['id']}}">
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
                    <button class="btn btn-secondary w-100 mb-2" onclick="loadData()">Загрузить схему</button>
                    <button class="btn btn-secondary w-100 mb-2" onclick="chekData()">Проверить данные</button>
                    <button class="btn btn-success w-100 mb-2"   onclick="nextPage()">Далее</button>
                </div>
                <div class="col-md-10" id="canvas-wrapper" >

                    <button class="btn btn-sm btn-moove-right" onclick="zoomRight()"><i class="bi bi-caret-right"></i></button>
                    <button class="btn btn-sm btn-moove-left"  onclick="zoomLeft()"><i class="bi bi-caret-left"></i></button>
                    <button class="btn btn-sm btn-moove-up"    onclick="zoomUp()"><i class="bi bi-caret-up"></i></button>
                    <button class="btn btn-sm btn-moove-down"  onclick="zoomDown()"><i class="bi bi-caret-down"></i></button>

                    <div id="canvas"></div>
                    <div id="canvas2"></div>
                    
                </div>
            </div>
        </div>
        
        <!-- окно для данных ЧРП -->
        <livewire:blocks.form-edit-modal-fr />

        <!-- окно для данных узлов -->
        <div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" wire:ignore>
                        <h5 class="modal-title" id="modal-title-node"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body" wire:loading.class="opacity-50">
                        
                        

                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-auto">
                                Название
                            </div>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input1" class="form-control mb-2" placeholder="Название или тип">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-auto">
                                Дополнительно
                            </div>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input2" class="form-control mb-2" placeholder="Дополнительно">
                            </div>
                        </div>

                        <hr />
                        <div style="width: 100%; text-align: center;">Фильтр добавления продукта</div>

                        <form wire:submit="searchProductForm">
                            @forelse($product_filter_select as $p_filter_key => $p_filter_value)

                            <div class="row g-3 align-items-center pb-1">
                                <div class="col-auto">
                                    <label for="inputPassword6" class="col-form-label">{{$p_filter_value['name']}}</label>
                                </div>
                                <div class="col-auto" style="margin-left:auto;">
                                
                                    <select class="form-select" id="product_filter_{{$p_filter_key}}"
                                        name="{{$p_filter_key}}"
                                        wire:model="getData.{{$p_filter_value['key']}}"
                                    >
                                        <option value="" wire:key="product_filter_field_null">---</option>
                                        @forelse($p_filter_value['fields'] as $fields_key => $fields_val)
                                        
                                            <option value="{{$fields_val}}" wire:key="product_filter_field_{{$fields_key}}">{{$fields_val}}</option>
                                        @empty
                                            <option value="">Нет данных</option>
                                        @endforelse
                                    </select>

                                </div>
                            </div>
                            @empty
                                <p>Нет фильтров для выбора</p>
                            @endforelse
                        </form>

                        <div style="width: 100%; text-align: center;">Правило цены</div>
                        @if($product_rules_select)
                        <form wire:submit="searchProductForm">
                            <div class="mt-2 small">
                                @foreach($product_rules_select as $p_rules_key => $p_rules_value)
                                    <label class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            id="p_rules_value{{$p_rules_key}}"
                                            wire:model="getRules.{{$p_rules_value['key']}}"
                                        >
                                        <span>{{ $p_rules_value['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </form>
                        @else
                            <p>Нет правил для выбора</p>
                        @endif

                    </div>
                    



                    <div class="modal-body" wire:loading>
                        Загрузка фильтра ...
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal" wire:loading.attr="disabled">Удалить</button>
                        <button type="submit" class="btn btn-primary" onclick="saveModal()" data-bs-dismiss="modal" wire:loading.attr="disabled">Сохранить</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- окно для данных кабеля -->
        <div class="modal fade" id="editModalCable" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" wire:ignore>
                        <h5 class="modal-title" id="modal-title-conn"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">


                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-auto">
                                Название
                            </div>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input10" class="form-control mb-2" placeholder="Кабель">
                            </div>
                        </div>
                        
                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-auto">
                                Длинна
                            </div>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input11" class="form-control mb-2" placeholder="1 метр">
                            </div>
                        </div>

                        <hr />
                        <div style="width: 100%; text-align: center;">Фильтр добавления продукта</div>

                        <form wire:submit="searchProductForm">
                            @forelse($product_filter_select as $p_filter_key => $p_filter_value)

                            <div class="row g-3 align-items-center pb-1">
                                <div class="col-auto">
                                    <label for="inputPassword6" class="col-form-label">{{$p_filter_value['name']}}</label>
                                </div>
                                <div class="col-auto" style="margin-left:auto;">
                                
                                    <select class="form-select" id="product_filter_{{$p_filter_key}}"
                                        name="{{$p_filter_key}}"
                                        wire:model="getData.{{$p_filter_value['key']}}"
                                    >
                                        <option value="" selected>---</option>
                                        @forelse($p_filter_value['fields'] as $fields_key => $fields_val)
                                            <option value="{{$fields_val}}" wire:key="product_filter_field_{{$fields_key}}">{{$fields_val}}</option>
                                        @empty
                                            <option value="">Нет данных</option>
                                        @endforelse
                                    </select>

                                </div>
                            </div>
                            @empty
                                <p>Нет фильтров для выбора</p>
                            @endforelse
                        </form>


                        <hr />
                        <div style="width: 100%; text-align: center;">Правило цены</div>
                        @if($product_rules_select)
                        <form wire:submit="searchProductForm">
                            <div class="mt-2 small">
                                @foreach($product_rules_select as $p_rules_key => $p_rules_value)
                                    <label class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            wire:model="getRules.{{$p_rules_value['key']}}"
                                        >
                                        <span>{{ $p_rules_value['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </form>
                        @else
                            <p>Нет правил для выбора</p>
                        @endif

                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" role="alert" wire:show="message_success">
                            {!! $message_success !!}
                        </div>
                        <div class="alert alert-danger" role="alert" wire:show="message_error">
                            {!! $message_error !!}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal">Удалить</button>
                        <button type="button" class="btn btn-primary" onclick="saveModal()">Сохранить</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </div>
        </div>


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

                // Рендер списка компонентов слева
                function renderComponents() {

                    canvas.style.width = savedSchema.page.width + 'px';
                    canvas.style.height = savedSchema.page.height + 'px';

                    const container = document.getElementById("components");
                    //container.innerHTML = "";
                    nodeSettings.forEach(item => {
                        //console.log(item);
                        const el = document.createElement("div");
                        el.className = "mb-2 border p-2 text-center";
                        el.setAttribute("draggable", "true");
                        el.setAttribute("data-type", item.type);
                        el.setAttribute("data-id", item.node_group.template.id);
                        el.innerHTML = `<img src="${item.image}" alt=""><div>${item.name}</div>`;
                        el.addEventListener("dragstart", e => {
                            e.dataTransfer.setData("type", item.type);
                        });
                        document.getElementById("flush-collapse-"+item.node_group.id).appendChild(el);

                        //container.appendChild(el);
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
                function createNode(type, x, y, savedId = null, savedName = "", savedExtra = "") {
                    const settings = nodeSettings.find(n => n.type === type);
                    if (!settings) return;
                    const node = document.createElement("div");
                    const id = savedId || "node" + Date.now(); //nodeIdCounter++;
                    const node_group_id = settings.node_group.id;
                    node.className = "node bg-danger";
                    node.title = "Нет привязки к продукту";
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
                    node.dataset.group_id = node_group_id;
                    
                    // для ЧРП отельное окно. 1 = группа ЧРП, остальные - для остальных продуктов. В дальнейшем можно будет расширить
                    if (node_group_id == 1) {
                        node.addEventListener("dblclick", () => {
                            modalTarget = node;
                            modalType = "node";
                            modalId = id;
                            document.getElementById("modal-input1").value = node.dataset.name;
                            document.getElementById("modal-input2").value = node.dataset.extra;
                            document.getElementById("modal-title-node").innerText = "Редактировать узел";

                            Livewire.dispatch('updateFilter', { template_id: settings.node_group.template.id, node_id: id });

                            const modal = new window.bootstrap.Modal(document.getElementById('editModalFR'));
                            modal.show();
                        });
                    // окно для остальных продуктов
                    } else {
                        node.addEventListener("dblclick", () => {
                            modalTarget = node;
                            modalType = "node";
                            modalId = id;
                            document.getElementById("modal-input1").value = node.dataset.name;
                            document.getElementById("modal-input2").value = node.dataset.extra;
                            document.getElementById("modal-title-node").innerText = "Редактировать узел";

                            Livewire.dispatch('updateFilter', { template_id: settings.node_group.template.id, node_id: id });

                            const modal = new window.bootstrap.Modal(document.getElementById('editModal'));
                            modal.show();
                        });
                    }

                    canvas.appendChild(node);
                    // Делаем узел перетаскиваемым
                    instance.draggable(node, {
                        
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
                            template_id: settings.node_group.template.id,
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
                    const template_id = 3;
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
                        const val1 = document.getElementById("modal-input1").value;
                        const val2 = document.getElementById("modal-input2").value;

                        modalTarget.dataset.name = val1;
                        modalTarget.dataset.extra = val2;
                        const label = modalTarget.querySelector(".label");
                        if (label) label.innerText = val1 + (val2 ? ` (${val2})` : "");
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
                    console.log("Текущая схема:", JSON.stringify(savedSchema, null, 2));
                    console.log("Текущая схема:", JSON.stringify(instance.connect, null, 2));
                    
                }
                // обновление после перемещения
                function positionUpdate() {
                    component.set('saved_schema', savedSchema);
                    
                }
                
                // 
                function chekData() {
                    console.log(JSON.parse(JSON.stringify(component.get('saved_schema'))));
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
                        }).catch(error => {
                            console.error("Caught error:", error.message || error);
                        });
                }
                




                function zoomRight() {
                    savedSchema.page.width = savedSchema.page.width + 100;
                    canvas.style.width = savedSchema.page.width + 'px';
                }
                function zoomLeft() {
                    savedSchema.page.width = savedSchema.page.width - 100;
                    canvas.style.width = savedSchema.page.width + 'px';
                }

                function zoomUp() {
                    savedSchema.page.height = savedSchema.page.height - 100;
                    canvas.style.height = savedSchema.page.height + 'px';
                }
                function zoomDown() {
                    savedSchema.page.height = savedSchema.page.height + 100;
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
                        if (c.product_id > 0){
                            document.getElementById(c.id).classList.remove('bg-danger');
                            document.getElementById(c.id).setAttribute('title', c.product_name);
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
