<template>
    <Card :style="styleModel" :bordered="false" dis-hover class="input-build-card">
        <p slot="title">
            <Icon v-if="icon" :type="icon"></Icon>
            {{title}}
        </p>
        <use-component :validate="validate" :control="control" :errorsValidate="errorsValidate" @changeValue="changeValue" :rules="componentsModel"></use-component>
    </Card>
</template>

<script>
    export default {
        name: "cardBuild",
        components:{
            useComponent:() => import('./useComponent'),
        },
        props:{
            styleModel: {
                type: String,
                default:''
            },
            icon: {
                type: String,
                default:''
            },
            title: {
                type: String,
                default:''
            },
            componentsModel: {
                type:Array,
                default(){
                    return [];
                }
            },
            validate: {
                type: Object,
                default() {
                    return {};
                }
            },
            errorsValidate: {
                type: Array,
                default() {
                    return [];
                }
            },
            control: {
                type: Array,
                default() {
                    return [];
                }
            },
        },
        date(){
            return {
                submitValue:{},
            };
        },
        mounted() {

        },
        methods:{
            changeValue(e){
                this.$emit('changeValue',{field:e.field,value:e.value});
            },
        },
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
