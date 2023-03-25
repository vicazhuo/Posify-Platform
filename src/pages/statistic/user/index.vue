<template>
  <div class="article-manager">
    <Card :bordered="false" dis-hover class="mt15 ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form ref="formInline"
        :label-width="labelWidth"
        :label-position="labelPosition"
         :model="formInline" inline>
            <FormItem label="用户渠道：">
              <Select
                v-model="channel_type"
                style="width: 250px"
                placeholder="用户渠道"
                @on-change="changeTxt"
              >
                <Option value="all">全部</Option>
                <Option value="wechat">公众号</Option>
                <Option value="routine">小程序</Option>
                <Option value="h5">H5</Option>
                <Option value="pc">PC</Option>
              </Select>
            </FormItem>
            <FormItem label="选择时间：">
              <DatePicker
                :editable="false"
                :clearable="false"
                @on-change="onchangeTime"
                :value="timeVal"
                format="yyyy/MM/dd"
                type="datetimerange"
                placement="bottom-start"
                placeholder="自定义时间"
                style="width: 250px"
                :options="options"
              ></DatePicker>
              <Button type="primary" class="btn-mx" @click="handleSubmit('formInline')">查询</Button>
              <Button type="primary" @click="exports">导出</Button>
            </FormItem>
          </Form>
      </div>
    </Card>
    <user-info :formInline="formInline" ref="userInfos" key="1"></user-info>
    <wechet-info
      :formInline="formInline"
      ref="wechetInfos"
      v-if="isShow"
      key="2"
    ></wechet-info>
    <user-region
      :formInline="formInline"
      ref="userRegions"
      key="3"
    ></user-region>
  </div>
</template>

<script>
import { mapState } from "vuex";
import userInfo from "./components/userInfo";
import wechetInfo from "./components/wechetInfo";
import userRegion from "./components/userRegion";
import { statisticUserExcel } from "@/api/statistic";
import { formatDate } from "@/utils/validate";
import exportExcel from "@/utils/newToExcel.js";

export default {
  name: "index",
  components: {
    userInfo,
    wechetInfo,
    userRegion,
  },
  data() {
    return {
      // options: this.$timeOptions,
	  options: {
	    shortcuts: [
	      {
	        text: "今天",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            new Date(
	              new Date().getFullYear(),
	              new Date().getMonth(),
	              new Date().getDate()
	            )
	          );
	          return [start, end];
	        },
	      },
	      {
	        text: "昨天",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            start.setTime(
	              new Date(
	                new Date().getFullYear(),
	                new Date().getMonth(),
	                new Date().getDate() - 1
	              )
	            )
	          );
	          end.setTime(
	            end.setTime(
	              new Date(
	                new Date().getFullYear(),
	                new Date().getMonth(),
	                new Date().getDate() - 1
	              )
	            )
	          );
	          return [start, end];
	        },
	      },
	      {
	        text: "最近7天",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            start.setTime(
	              new Date(
	                new Date().getFullYear(),
	                new Date().getMonth(),
	                new Date().getDate() - 6
	              )
	            )
	          );
	          return [start, end];
	        },
	      },
	      {
	        text: "最近30天",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            start.setTime(
	              new Date(
	                new Date().getFullYear(),
	                new Date().getMonth(),
	                new Date().getDate() - 29
	              )
	            )
	          );
	          return [start, end];
	        },
	      },
	      {
	        text: "本月",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            start.setTime(
	              new Date(new Date().getFullYear(), new Date().getMonth(), 1)
	            )
	          );
	          return [start, end];
	        },
	      },
	      {
	        text: "本年",
	        value() {
	          const end = new Date();
	          const start = new Date();
	          start.setTime(
	            start.setTime(new Date(new Date().getFullYear(), 0, 1))
	          );
	          return [start, end];
	        },
	      },
	    ],
	  },
      formInline: {
        channel_type: "",
        data: "",
      },
      channel_type: "",
      timeVal: [],
      isShow: false,
    };
  },
  computed:{
     ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    const end = new Date();
    const start = new Date();
    start.setTime(
      start.setTime(
        new Date(
          new Date().getFullYear(),
          new Date().getMonth(),
          new Date().getDate() - 29
        )
      )
    );
    this.timeVal = [start, end];
    this.formInline.data =
      formatDate(start, "yyyy/MM/dd") + "-" + formatDate(end, "yyyy/MM/dd");
  },
  methods: {
    changeTxt() {
      this.formInline.channel_type =
        this.channel_type === "all" ? "" : this.channel_type;
    },
    // // 导出
    // excel() {
    //   statisticUserExcel(this.formInline).then(async (res) => {
    //     res.data.url.map((item) => {
    //       window.location.href = item;
    //     });
    //   });
    // },
    // 数据导出；
    async exports() {
      let [th, filekey, data, fileName] = [[], [], [], ""];
      let excelData = JSON.parse(JSON.stringify(this.formInline));
      let lebData = await this.getExcelData(excelData);
      if (!fileName) fileName = lebData.filename;
      if (!filekey.length) {
        filekey = lebData.filekey;
      }
      if (!th.length) th = lebData.header;
      data = data.concat(lebData.export);
      exportExcel(th, filekey, fileName, data);
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        statisticUserExcel(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formInline.data = this.timeVal.join("-");
    },
    handleSubmit() {
      this.$refs.userInfos.getStatistics();
      this.$refs.userInfos.getTrend();
      this.$refs.userRegions.getTrend();
      this.$refs.userRegions.getSex();
      if (this.formInline.channel_type === "wechat") {
        this.isShow = true;
        this.$refs.wechetInfos.getStatistics();
        this.$refs.wechetInfos.getTrend();
      } else {
        this.isShow = false;
      }
    },
  },
};
</script>

<style scoped>
.btn-mx{
  margin:0 14px 0;
}
</style>