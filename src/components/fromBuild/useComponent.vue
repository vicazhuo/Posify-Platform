<template>
    <div>
        <!-- 引用组件-->
        <template v-for="item in rules">
            <component :validate="validate" :maxlength="item.maxlength" :min="item.min" :max="item.max" :randToken="item.randToken" :copy="item.copy" :copyText="item.copyText" :prefix="item.prefix" :disabled="item.disabled" :vertical="item.vertical" :showIcon="item.showIcon" :closable="item.closable" :type="item.type" :rows="item.rows" :control="item.control" :errorsValidate="errorsValidate" :info="item.info" :componentsModel="item.componentsModel" :icon="item.icon" :filterable="item.filterable" :multiple="item.multiple" :options="item.options" :upload="item.upload"  @changeValue="changeValue" :is="item.name +'Build' " :field="item.field" :on="item.on" :title="item.title" :style="item.style" :value="item.value" :name="item.name"></component>
        </template>
    </div>
</template>

<script>
    import components from "./index";

    export default {
        name: "useComponent",
        mixins:[components],
        props:{
            //表单规则
            rules:{
                type:Array,
                default(){
                    return [];
                }
            },
            //表单验证
            validate:{
                type:Object,
                default() {
                    return {};
                }
            },
            //全部错误
            errorsValidate:{
                type:Array,
                default(){
                    return [];
                }
            }
        },
        methods:{
            changeValue(e){
                this.$emit('changeValue',{field:e.field,value:e.value});
            },
        }
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
