<template>
  <div class="message">
    <div class="i-layout-page-header">
      <!-- <PageHeader class="product_tabs" :title="$route.meta.title">
        <div slot="content">
          
        </div>
      </PageHeader> -->
      <div class="table-box">
        <Card :bordered="false" dis-hover class="ivu-mt">
          <div class="new_tab">
            <Tabs v-model="currentTab" @on-click="changeTab">
              <TabPane
                :label="item.label"
                :name="item.value.toString()"
                v-for="(item, index) in headerList"
                :key="index"
              />
            </Tabs>
          </div>
          <Row type="flex" class="mb20" v-if="currentTab==1">
            <Col>
              <Button
                v-auth="['app-wechat-template-sync']"
                type="success"
                @click="syncTemplate"
                style="margin-left: 20px"
                >同步小程序订阅消息</Button
              >
							<Button
							  v-auth="['app-wechat-wechat-sync']"
							  type="primary"
							  @click="wechatTemplate"
							  style="margin-left: 20px"
							  >同步公众号模板消息</Button
							>
            </Col>
          </Row>
          <Alert v-if="industry" closable>
            <template slot="desc">
              <div>
                主营行业：{{
                  industry.primary_industry.first_class
                    ? industry.primary_industry.first_class + "||"
                    : industry.primary_industry
                }}
                {{
                  industry.primary_industry.second_class
                    ? industry.primary_industry.second_class
                    : ""
                }}
              </div>
              <div>
                副营行业：{{
                  industry.secondary_industry.first_class
                    ? industry.primary_industry.first_class + "||"
                    : industry.primary_industry
                }}
                {{
                  industry.primary_industry.second_class
                    ? industry.primary_industry.second_class
                    : ""
                }}
              </div>
			  <div>注意：小程序需选择生活服务/百货/超市/便利店类目，否则不能同步小程序订阅消息</div>
            </template>
          </Alert>
          <Table
            :columns="columns"
            :data="levelLists"
            ref="table"
            class="mt25"
            :loading="loading"
            highlight-row
            no-userFrom-text="暂无数据"
            no-filtered-userFrom-text="暂无筛选结果"
          >
            <template slot-scope="{ row, index }" slot="name">
              <span class="table">
                {{ row.name }}
              </span>
            </template>
            <template slot-scope="{ row, index }" slot="title">
              <span class="table">{{ row.title }}</span>
            </template>
            <template
              slot-scope="{ row, index }"
              v-for="(item, index) in [
                'is_system',
                'is_wechat',
                'is_routine',
                'is_sms',
                'is_ent_wechat',
              ]"
              :slot="item"
            >
              <i-switch
                v-model="row[item]"
                :value="row[item]"
                :true-value="1"
                :false-value="2"
                @on-change="changeSwitch(row, item)"
                size="large"
                v-if="row[item] > 0"
              >
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
              </i-switch>
            </template>
            <template slot-scope="{ row, index }" slot="setting">
              <span class="setting btn" @click="setting(item, row)">设置</span>
            </template>
          </Table>
        </Card>
      </div>
    </div>
  </div>
</template>

<script>
import {
  getNotificationList,
  getNotificationInfo,
  noticeStatus,
} from "@/api/notification.js";
import { routineSyncTemplate, wechatSyncTemplate } from "@/api/app";
export default {
  data() {
    return {
      modalTitle: "",
      notificationModal: false,
      headerList: [
        { label: "通知会员", value: "1" },
        { label: "通知平台", value: "2" },
      ],
      columns: [
        {
          title: "ID",
          key: "id",
          align: "center",
          width: 50,
        },
        {
          title: "通知类型",
          slot: "name",
          align: "center",
          width: 200,
        },
        {
          title: "通知场景说明",
          slot: "title",
          align: "center",
          minWidth: 200,
        },
        {
          title: "站内信",
          slot: "is_system",
          align: "center",
          minWidth: 100,
        },
        {
          title: "公众号模板",
          slot: "is_wechat",
          align: "center",
          minWidth: 100,
        },
        {
          title: "小程序订阅",
          slot: "is_routine",
          align: "center",
          minWidth: 100,
        },
        {
          title: "发送短信",
          slot: "is_sms",
          align: "center",
          minWidth: 100,
        },
        {
          title: "企业微信",
          slot: "is_ent_wechat",
          align: "center",
          minWidth: 100,
        },
        {
          title: "设置",
          slot: "setting",
          width: 150,
          align: "center",
        },
      ],
      levelLists: [],
      currentTab: "1",
      loading: false,
      formData: {},
      industry: null,
    };
  },
  created() {
    this.changeTab(this.currentTab);
  },
  methods: {
    changeSwitch(row, item) {
      noticeStatus(item, row[item], row.id)
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    changeTab(data) {
      getNotificationList(data).then((res) => {
        this.levelLists = res.data.list;
        this.industry = res.data.industry;
      });
    },
    // 同步订阅消息
    syncTemplate() {
      routineSyncTemplate()
        .then((res) => {
          this.$Message.success(res.msg);
          this.changeTab(this.currentTab);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
		// 同步公众号模板消息
		wechatTemplate() {
		  wechatSyncTemplate()
		    .then((res) => {
		      this.$Message.success(res.msg);
		      this.changeTab(this.currentTab);
		    })
		    .catch((res) => {
		      this.$Message.error(res.msg);
		    });
		},
    // 开启关闭
    changeStatus() {},
    // 列表
    notice() {},
    // 设置
    setting(item, row) {
      this.$router.push({
        path: "/admin/setting/notification/notificationEdit?id=" + row.id,
      });
      // console.log(item);
    },
    getData(keys, row, item) {
      this.formData = {};
      getNotificationInfo(row.id, item).then((res) => {
        keys.map((i, v) => {
          this.formData[i] = res.data[i];
        });
        this.formData.type = item;
        this.notificationModal = true;
      });
    },
  },
};
</script>

<style scoped lang="stylus">
.message /deep/ .ivu-table-header table {
  /* border-top: 1px solid #e8eaec !important;
  border-left: 1px solid #e8eaec !important; */
}
.message /deep/ .ivu-table-header thead tr th {
  padding: 8px 16px;
}
.message /deep/ .ivu-tabs-tab {
  border-radius: 0 !important;
}
/* .table-box {
  padding: 20px;
} */
.is-table {
  display: flex;
  /* justify-content: space-around; */
  justify-content: center;
}
.btn {
  padding: 6px 12px;
  cursor: pointer;
  color: #2d8cf0;
  font-size: 10px;
  border-radius: 3px;
}
.is-switch-close {
  background-color: #504444;
}
.is-switch {
  background-color: #eb5252;
}
.notice-list {
  background-color: #308cf5;
  margin: 0 15px;
}
.table {
  padding: 0 18px;
}
.new_tab {
		>>>.ivu-tabs-nav .ivu-tabs-tab{
			padding:4px 16px 20px !important;
			font-weight: 500;
		}
	}
</style>
