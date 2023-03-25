<template>
    <div>
        <Card :bordered="false" :padding="0" dis-hover class="ivu-mt">
            <Row type="flex" :wrap="false">
                <Col flex="none">
                    <Menu :active-name="activeName" width="auto" @on-select="menuSelect">
                        <MenuItem name="1">微信关注回复</MenuItem>
                        <MenuItem name="2">关键字回复</MenuItem>
                        <MenuItem name="3">无效关键字回复</MenuItem>
                    </Menu>
                </Col>
                <Col flex="auto">
                    <ReplyKeyword v-if="activeName === '2' && !keywordSave" :active-name="activeName" @keyword-action="keywordAction" />
                    <ReplyFollow v-if="activeName !== '2' || keywordSave" :active-name="activeName" :keyword-save="keywordSave" @follow-action="followAction" />
                </Col>
            </Row>
        </Card>
    </div>
</template>

<script>
import ReplyFollow from "./follow";
import ReplyKeyword from "./keyword";

    export default {
        name: 'index',
        components: {
            ReplyFollow,
            ReplyKeyword
        },
        data () {
            return {
                activeName: '1',
                keywordSave: ''
            };
        },
        // watch: {
        //     activeName: {
        //         handler (value) {
        //             switch (value) {
        //                 case '1':
        //                     this.keywordSave = 'subscribe';
        //                     break;
        //                 case '3':
        //                     this.keywordSave = 'default';
        //                     break;
        //                 default:
        //                     this.keywordSave = '';
        //                     break;
        //             }
        //         },
        //         immediate: true
        //     }
        // },
        methods: {
            menuSelect (name) {
                this.activeName = name;
                this.followAction();
            },
            keywordAction (value) {
                this.keywordSave = value;
            },
            followAction () {
                this.keywordSave = '';
            }
        }
    }
</script>

<style lang="less" scoped>
.ivu-card {
    border-radius: 6px;
    overflow: hidden;
}

.ivu-menu {
    z-index: auto;
    height: 100%;
}
</style>
