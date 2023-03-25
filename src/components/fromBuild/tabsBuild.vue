<template>
    <div class="new_tab">
        <Tabs>
            <TabPane :label="item.label" v-for="item in options">
                <use-component :validate="validate"  :control="control" :errorsValidate="errorsValidate" @changeValue="changeValue" :rules="item.componentsModel" />
            </TabPane>
        </Tabs>
    </div>
</template>

<script>
    import build from "./build";
    import useComponent from "./useComponent";

    export default {
        name: "tabsBuild",
        mixins:[build],
        components:{useComponent},
        props: {
            control: {
                type: Array,
                default() {
                    return [];
                }
            },
        },
        methods:{
            changeValue(e){
                this.$emit('changeValue',{field:e.field,value:e.value});
            },
        }
    }
</script>

<style scoped lang="stylus">
    @import url('./css/build.css');
    .ivu-tabs {
        background-color: #ffffff;
        margin-bottom: 30px;
        padding: 20px;
        border-radius: 6px;
    }
    .new_tab {
    >>>.ivu-tabs-nav .ivu-tabs-tab{
        padding:4px 16px 20px !important;
        font-weight: 500;
    }
    }
</style>
