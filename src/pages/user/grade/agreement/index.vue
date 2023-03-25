<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Form :label-width="80" @submit.native.prevent>
                <FormItem label="协议名称：">
                    <Input v-model="agreement.title"></Input>
                </FormItem>
                <FormItem label="协议内容：">
					<WangEditor
					  :content="content"
					  @editorContent="getEditorContent"
					></WangEditor>
                </FormItem>
                <FormItem label="开启状态：">
                    <i-switch
                        v-model="agreement.status"
                        size="large"
                        :true-value="1"
                        :false-value="0"
                    >
                        <span slot="open">开启</span>
                        <span slot="close">关闭</span>
                    </i-switch>
                </FormItem>
            </Form>
            <Spin fix v-if="spinShow"></Spin>
        </Card>
		<!-- <Button type="primary" @click="memberAgreementSave">保存</Button> -->
		<Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
			<div class="acea-row row-center">
				<Button type="primary" @click="memberAgreementSave">保存</Button>
			</div>
		</Card>
    </div>
</template>

<script>
import WangEditor from "@/components/wangEditor/index.vue";
import { memberAgreement, memberAgreementSave } from "@/api/user";
import { mapState,mapMutations } from "vuex";
export default {
    components: { WangEditor },
    data() {
        return {
            ueConfig: {
                autoHeightEnabled: false,
                initialFrameHeight: 500,
                initialFrameWidth: "100%",
                UEDITOR_HOME_URL: "/admin/UEditor/",
                serverUrl: ""
            },
            id: 0,
            agreement: {
                title: "",
                content: "",
                status: 1
            },
            content:'',
            spinShow:false
        };
    },
    created() {
        this.memberAgreement();
    },
	computed: {
	  ...mapState("admin/layout", ["isMobile","menuCollapse"]),
	  labelWidth() {
	    return this.isMobile ? undefined : 120;
	  },
	  labelPosition() {
	    return this.isMobile ? "top" : "right";
	  },
	  labelBottom() {
	    return this.isMobile ? undefined : 15;
	  }
	},
    methods: {
		getEditorContent(data){
		    this.agreement.content = data
		},
        memberAgreement() {
            this.spinShow = true;
            memberAgreement()
                .then(res => {
                    this.spinShow = false;
                    const { title, content, status, id } = res.data;
                    this.agreement.title = title;
                    this.agreement.content = content;
                    this.content = content;
                    this.agreement.status = status;
                    this.id = id;
                })
                .catch(err => {
                    this.$Message.error(err);
                    this.spinShow = false;
                });
        },
        // 保存
        memberAgreementSave() {
            this.$Spin.show();
            memberAgreementSave(this.id, this.agreement)
                .then(res => {
                    this.$Spin.hide();
                    this.$Message.success("保存成功");
                    this.memberAgreement();
                })
                .catch(err => {
                    this.$Spin.hide();
                    this.$Message.error(err.msg);
                });
        }
    }
};
</script>

<style scoped lang="stylus">
	.fixed-card {
	    position: fixed;
	    right: 0;
	    bottom: 0;
	    left: 200px;
	    z-index: 99;
	    box-shadow: 0 -1px 2px rgb(240, 240, 240);
	
	    /deep/ .ivu-card-body {
	        padding: 15px 16px 14px;
	    }
	
	    .ivu-form-item {
	        margin-bottom: 0;
	    }
	
	    /deep/ .ivu-form-item-content {
	        margin-right: 124px;
	        text-align: center;
	    }
	
	    .ivu-btn {
	        height: 36px;
	        padding: 0 20px;
	    }
	}
</style>
