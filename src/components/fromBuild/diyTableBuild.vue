<template>
    <div class="diy-table">
        <FormItem :label="title" class="input-build" :class="getClassName()">
            <Table :columns="columns" :data="valueModel" :width="732" class="diy_table">
                <template slot-scope="{ row, index }" :slot="item.key" v-for="item in options">
                    <template v-if="item.type === 'input'">
                        <Input type="text" v-model="valueModel[index][item.key]" style="width:150px;" />
                    </template>
                    <template v-else-if="item.type === 'select'">
                        <Select v-model="valueModel[index][item.key]" >
                            <Option v-for="vv in item.props.options || []" :value="vv.value" :key="vv.value">{{ vv.label }}</Option>
                        </Select>
                    </template>
                    <template v-else-if="item.type === 'inputNumber'">
                        <InputNumber v-model="valueModel[index][item.key]" :editable="item.props.editable || false" :name="field" :min="0" style="width:150px;" />
                    </template>
                    <template v-else-if="item.type === 'switch'">
                        <Switch size="large" v-model="valueModel[index][item.key]" :true-value="1" :false-value="0">
                            <span slot="open">开启</span>
                            <span slot="close">关闭</span>
                        </Switch>
                    </template>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <span class="delete" @click="del(index)">删除</span>
                </template>
            </Table>
            <div class="diy-button">
                <Button type="primary" @click="add">添加</Button>
            </div>
        </FormItem>
    </div>
</template>

<script>
    import build from "./build";
    export default {
        name: "diyTabelBuild",
        mixins: [build],
        watch: {
            valueModel: {
                handler(n) {
                    this.changeValue();
                },
                deep: true
            },
        },
        methods: {
            add() {
                let value = {};
                this.options.map(item => {
                    if (item.key === 'sort') {
                        value[item.key] = 0;
                    } else {
                        value[item.key] = '';
                    }
                })
                this.valueModel.push(value);
            },
            del(index) {
                this.valueModel.splice(index,1);
            },
            changeValue() {
                this.$emit('changeValue', {field: this.field,value: this.valueModel});
                //触发change事件
                this.on['change'] && this.on['change'](this.valueModel);
            },
        },
        computed: {
            columns(){
                let columns = [];
                this.options.map(item => {
                    columns.push({title:item.name,slot:item.key,align:item.alert || 'left'});
                });
                columns.push({title:'操作', slot:'action', width:100, align:'left'});
                return columns;
            },
        },
        data() {
            return {
                data: [],
            }
        },
        mounted() {

        }
    }
</script>

<style scoped lang="stylus">
    @import url('./css/build.css');

    .diy-table .diy-button{
        margin-top: 10px;
    }
    .diy_table{
        box-sizing: border-box;
        position: relative;
        &:before{
            content:"";
            width:1px;
            height:100%;
            background: #eee;
            position: absolute;
            top:0;
            left:0;
        }
        &:after{
            content:"";
            width:1px;
            height:100%;
            background: #eee;
            position: absolute;
            top:0;
            right:0;
        }
    }
    .delete{
        color: #57a3f3;
    }
</style>
