<title>js-block-diagram</title>
<link rel="stylesheet" type="text/css" href="/vendor/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="/vendor/font-awesome-4.5.0/css/font-awesome.min.css">

<div id="app" class="container">
    <iframe id="my_iframe" style="display:none;"></iframe>
    <div class="row">
        <div class="col-md-8">
            <diagram-component
                :blocks="blocks"
                :links="links"
            ></diagram-component>
        </div>
        <div class="col-md-4">
            <var-list-component
                :variables="variables"
            ></var-list-component>
            <var-editor-component
                v-if="active_block"
                transition="fadein"
            ></var-editor-component>
            <block-list-component
                :current_id="current_id"
                :blocks.sync="blocks"
            ></block-list-component>
            <block-editor-component
                v-if="active_block"
                transition="fadein"
                :block.sync="active_block"
            ></block-editor-component>
            <div class="bottom-margin">
                <form action="<?php echo url_for('generate'); ?>" method="post">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fa fa-cogs"></i> Generuj
                    </button>
                    <input name="data" type="hidden" :value='exported_data|json'>
                </form>
                <button id ="btnSave" class = "btn btn-success btn-lg" >
                    <i class="fa fa-save"></i> Zapisz do pliku
                </button>
            </div>
        </div>
    </div>
</div>

<template id="diagram-component">
    <div id="diagram-component">
        <template v-for="link in links">
            <link-component
                :link="link"
                :blocks="blocks"
            ></link-component>
        </template>
        <template v-for="block in blocks">
            <block-component :block="block"></block-component>
        </template>
        <template v-for="variable in variables">
        </template>
    </div>
</template>

<template id="link-component">
    <svg class="link-component">
        <line
            :x1="a.x"
            :y1="a.y"
            :x2="b.x"
            :y2="b.y"
            style="stroke:#333;stroke-width:2"
        />
        <circle :cx="b.x" :cy="b.y" r="3"/>
    </svg>
</template>

<template id="variable-component">
</template>

<template id="block-component">
    <div
        class="block-component"
        v-bind:style="{
				top: block.position.y + 'px',
				left: block.position.x + 'px'
			}"

        :data-id="block.id"

        draggable='true'
        @dragstart="dragStart"
        @dragend="dragEnd"
        @click="$dispatch('active_block:set', block)"
    >
        <svg
            v-if="block.type==='operand'">
            <rect
                height="100%"
                width="100%"
                style="fill:rgb(140,140,190);stroke-width:3;stroke:rgb(0,0,0)"/>
            <text x="5" y="20" fill="#554466">{{block.text}}</text>
        </svg>
        <svg
            v-if="block.type==='predicate'"
            height="35"
            width="140">
            <polygon class="block-predicate" points="0,17, 70,0, 140,17, 70,35"/>
            <text x="25" y="20" fill="#554466">{{block.text}}</text>
        </svg>
        <svg
            v-if="block.type==='endpredicate'"
            height="35"
            width="140">
            <polygon class="block-endpredicate" points="0,17, 70,0, 140,17, 70,35"/>
            <text x="25" y="20" fill="#554466">{{block.text}}</text>
        </svg>
        <svg
            v-if="block.type==='output'">
            <polygon points="0,0 140,0 70,35" class="block-output"/>
            <text x="25" y="20" fill="#554466">{{block.text}}</text>
        </svg>
        <svg
            v-if="block.type==='input'">
            <polygon points="70,0 0,35 140,35" class="block-input"/>
            <text x="25" y="20" fill="#554466">{{block.text}}</text>
        </svg>

        <div
            class="attachment attachment-top"
            :class="{active: active_attachments.top}"

            @click="attachmentClicked('top')"
        ></div>

        <template v-if="block.type==='predicate'">
            <div
                class="attachment attachment-left"
                :class="{active: active_attachments.left}"

                @click="attachmentClicked('left')"
            ></div>
            <div
                class="attachment attachment-right"
                :class="{active: active_attachments.right}"

                @click="attachmentClicked('right')"
            ></div>
        </template>

        <template v-if="block.type==='endpredicate'">
            <div
                class="attachment attachment-bottom"
                :class="{active: active_attachments.bottom}"

                @click="attachmentClicked('bottom')"
            ></div>
        </template>

        <template v-if="block.type==='operand'">
            <div
                class="attachment attachment-bottom"
                :class="{active: active_attachments.bottom}"

                @click="attachmentClicked('bottom')"
            ></div>
        </template>

        <template v-if="block.type==='output'">
            <div
                class="attachment attachment-bottom"
                :class="{active: active_attachments.bottom}"

                @click="attachmentClicked('bottom')"
            ></div>
        </template>

        <template v-if="block.type==='input'">
            <div
                class="attachment attachment-bottom"
                :class="{active: active_attachments.bottom}"

                @click="attachmentClicked('bottom')"
            ></div>
        </template>

    </div>
</template>

<template id="block-editor-component">
    <div id="block-editor-component" class="bottom-margin">
        <div class="input-group">
            <label for="block-text-input">Text: </label>
            <input
                id="block-text-input"
                class="form-control"
                type="text"
                v-model="block.text"
            >
        </div>
    </div>
</template>

<template id="var-editor-component">
    <div id="var-editor-component" class="bottom-margin">
        <div class="input-group">
            <label for="var-text-input">Name: </label>
            <input
                id="var-text-input"
                class="form-control"
                type="text"
                v-model="variable.name"
            >
        </div>
    </div>
</template>


<template id="block-list-component">
    <div id="block-list-component" class="bottom-margin">
        <table class="table table-bordered table-condensed">
            <tr>
                <th>type</th>
                <th>text</th>
                <th></th>
            </tr>
            <tr v-for="block in blocks" :class="cssClass(block)">
                <td class="text-center">
                    <i class="fa fa-square"></i>
                </td>
                <td>
                    <a href="#" @click="$dispatch('active_block:set', block)">{{block.text}}</a>
                </td>
                <td>
                    <a href="#" class="btn btn-xs btn-warning"
                       @click="remove(block)"
                    >
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
        </table>
        <div>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="createBlock('predicate')"
            >
                <i class="fa fa-plus"></i> predicate
            </a>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="createBlock('endpredicate')"
            >
                <i class="fa fa-plus"></i> endpredicate
            </a>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="createBlock('operand')"
            >
                <i class="fa fa-plus"></i> operand
            </a>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="createBlock('input')"
            >
                <i class="fa fa-plus"></i> input
            </a>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="createBlock('output')"
            >
                <i class="fa fa-plus"></i> output
            </a>
        </div>
    </div>
</template>

<template id="var-list-component">
    <div id="var-list-component" class="bottom-margin">
        <table class="table table-bordered table-condensed">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th></th>
            </tr>
            <tr v-for="variable in variables"
            ">
            <td class="text-center">
                {{variable.name}}
            </td>
            <td>
                {{variable.type}}
            </td>
            <td>
                <a href="#" class="btn btn-xs btn-warning"
                   @click="removeVariable(variable)"
                >
                    <i class="fa fa-times"></i>
                </a>
            </td>
            </tr>
        </table>
        <div>
            <a
                href="#"
                class="btn btn-primary btn-sm"
                @click="addVariable()"
            >
                <i class="fa fa-plus"></i> variable
            </a>
            <button type="button" id="btnPopupShow" class="btn btn-primary btn-sm">Variable</button>

        </div>
        <div class="messagepop pop">
            <p><label for="name">Variable Name</label><input type="text" size="30" name="var-name" id="var-name"/></p>

            <p><label for="type">Variable Type</label><input type="text" size="30" name="var-type" id="var-type"/></p>

            <p><a
                    href="#"
                    class="btn btn-primary btn-sm"
                    @click="addVariable()"
                >
                    <i class="fa fa-plus"></i> Add
                </a> or <a class="close" href="/">Cancel</a></p>
        </div>
    </div>
</template>


<script src="/vendor/vue.js"></script>
<script src="/vendor/jquery-2.2.1.min.js"></script>
<script src="/js/index.js"></script>

<script>
    $(document).ready(function () {
        /*  $("btnPopupShow").onClick(function () {
         $('#popupEventForm').show();
         // alert('pizda');
         });*/

        function deselect(e) {
            $('.pop').slideFadeToggle(function () {
                e.removeClass('selected');
            });
        }

        $(function () {
            $('#btnPopupShow').on('click', function () {
                if ($(this).hasClass('selected')) {
                    deselect($(this));
                } else {
                    $(this).addClass('selected');
                    $('.pop').slideFadeToggle();
                }
                return false;
            });

            $('.close').on('click', function () {
                deselect($('#btnPopupShow'));
                return false;
            });

        });

        $.fn.slideFadeToggle = function (easing, callback) {
            return this.animate({opacity: 'toggle', height: 'toggle'}, 'fast', easing, callback);
        };


        $('#btnSave').click(function () {

            var dataRow = {
                'data': $('input[name=data]').val(),
            }
            //alert("<?php echo url_for('saveToFile'); ?>");
            $.ajax({
                type: 'POST',
                url: "<?php echo url_for('saveToFile'); ?>",
                data: dataRow,
                success: function (response) {
                    alert(response);
                   // document.getElementById('my_iframe').src = response;
                    window.location.href="<?php echo url_for('@saveToFile');?>"
                    //location.reload();
                }
            });
        });

    });

</script>
