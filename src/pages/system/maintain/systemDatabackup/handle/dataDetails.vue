<template>
    <div>
        <Drawer :closable="false" width="840" v-model="modals" :title="tablename">
            <Table ref="selection" :columns="columns2" :data="tabList3" 
                   no-data-text="暂无数据" highlight-row  size="small"
                   no-filtered-data-text="暂无筛选结果">
            </Table>
        </Drawer>
    </div>
</template>
<script>
import { backupReadListApi } from '@/api/system';
    export default {
        name: 'dataDetails',
        
        data () {
            return {
                modals: false,
                tablename: '表',
                tabList3: [],
                columns2: [
                    {
                        title: '字段名',
                        key: 'COLUMN_NAME'
                    },
                    {
                        title: '数据类型',
                        key: 'COLUMN_TYPE'
                    },
                    {
                        title: '默认值',
                        key: 'COLUMN_DEFAULT'
                    },
                    {
                        title: '允许非空',
                        key: 'IS_NULLABLE'
                    },
                    {
                        title: '自动递增',
                        key: 'EXTRA'
                    },
                    {
                        title: '备注',
                        key: 'COLUMN_COMMENT'
                    }
                ],
                rows: {},
                dataList: {},
            }
        },
        methods: {
        // 表详情
            getDetails (row) {
                this.tablename = row.comment + '[' + row.name + ']' 
                let data = {
                    tablename: row.name
                };
                backupReadListApi(data).then(async res => {
                    let data = res.data
                    this.tabList3= data.list;
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
        }
    }
</script>
<style>
    .demo-drawer-profile{
        font-size: 14px;
    }
    .demo-drawer-profile .ivu-col{
        margin-bottom: 12px;
    }
</style>
