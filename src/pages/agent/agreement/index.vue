<template>
  <div class="form-submit">
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Form :label-width="80" @submit.native.prevent>
       <!-- <FormItem label="协议名称：">
          <Input v-model="agent.title"></Input>
        </FormItem> -->
        <FormItem label="说明内容：">
          <WangEditor
            :content="content"
            @editorContent="getEditorContent"
          ></WangEditor>
        </FormItem>
        <!-- <FormItem>
          <Button type="primary" @click="agentAgreementSave">保存</Button>
        </FormItem> -->
      </Form>
      <Spin fix v-if="spinShow"></Spin>
    </Card>
      <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
			<Form>
        <FormItem>
          <Button type="primary" @click="agentAgreementSave">保存</Button>
        </FormItem>
			</Form>
		</Card>	
  </div>
</template>

<script>
import { mapState } from "vuex";
import WangEditor from "@/components/wangEditor/index.vue";
import { agentAgreement, agentAgreementSave } from "@/api/user";

export default {
  components: { WangEditor },
	computed: {
	  ...mapState("admin/layout", ["isMobile","menuCollapse"])
	},
  data() {
    return {
      ueConfig: {
        autoHeightEnabled: false,
        initialFrameHeight: 500,
        initialFrameWidth: "100%",
        UEDITOR_HOME_URL: "/admin/UEditor/",
        serverUrl: "",
      },
      id: 0,
      agent: {
        // title: "",
        content: "",
        status: 1,
      },
      content:'',
      spinShow: false,
    };
  },
  created() {
    this.agentAgreement();
  },
  methods: {
    getEditorContent(data) {
      this.agent.content = data;
    },
    agentAgreement() {
      this.spinShow = true;
      agentAgreement()
        .then((res) => {
          this.spinShow = false;
          if (res.data.title) {
            const { title, content, status, id } = res.data;
            // this.agent.title = title;
            this.agent.content = content;
            this.content = content;
            this.agent.status = status;
            this.id = id || 0;
          }
        })
        .catch((err) => {
          this.$Message.error(err);
          this.spinShow = false;
        });
    },
    // 保存
    agentAgreementSave() {
      this.$Spin.show();
      agentAgreementSave(this.id, this.agent)
        .then((res) => {
          this.$Spin.hide();
          this.$Message.success("保存成功");
          this.agentAgreement();
        })
        .catch((err) => {
          this.$Spin.hide();
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>

<style scoped lang="stylus">
	/deep/.ivu-card-body{
		padding-top 40px;
	}
	.form-submit {
		  /deep/.ivu-card{
		  	border-radius: 0;
		  }
	    margin-bottom: 79px;
	
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
	}
</style>
