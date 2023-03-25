<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/group_chat' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">群统计</span>
        </div>
      </PageHeader>
    </div>
    <cards-data
      :cardLists="cardLists"
      v-if="cardLists.length >= 0"
    ></cards-data>
    <Card :bordered="false" dis-hover>
      <div class="acea-row row-between mb20">
        <div class="header_title">群成员统计</div>
        <div class="acea-row row-middle">
          <span class="font-sm">时间筛选：</span>
          <DatePicker
            :editable="false"
            @on-change="onchangeTime"
            :value="timeVal"
            format="yyyy/MM/dd"
            type="datetimerange"
            placement="bottom-start"
            placeholder="自定义时间"
            style="width: 250px"
            :options="options"
          ></DatePicker>
        </div>
      </div>
      <echarts-new
        :option-data="optionData"
        :styles="style"
        height="100%"
        width="100%"
        v-if="optionData"
      ></echarts-new>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table :columns="tableColumn" :data="tableData.list" :loading="userLoading">
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="tableData.count"
          :current="tableForm.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="tableForm.limit"
        />
      </div>
    </Card>
  </div>
</template>
<script>
import cardsData from "@/components/cards/cards";
import { workGroupStatistics, workGroupStatisticsList } from "@/api/work";
import echartsNew from "@/components/echartsNew/index";
import timeOptions from "@/utils/timeOptions";
export default {
  data() {
    return {
      cardLists: [],
      optionData: {},
      style: { height: "400px" },
      spinShow: false,
      tableColumn: [
        {
          title: "时间",
          key: "create_time",
          minWidth: 120,
          align: "left",
        },
        {
          title: "新增人员",
          key: "today_sum",
          minWidth: 100,
          align: "left",
        },
        {
          title: "退群人员",
          key: "chat_return_sum",
          minWidth: 100,
          align: "left",
        },
        {
          title: "当前群人员",
          key: "chat_sum",
          minWidth: 100,
          align: "left",
        },
        {
          title: "当前退群人员",
          key: "today_return_sum",
          minWidth: 100,
          align: "center",
        },
      ],
      tableData: [],
      userLoading: false,
     options: timeOptions,
      timeVal: [],
      tableForm: {
        page: 1,
        time:"",
        limit: 15,
        id: 0,
      },
    };
  },
  components: { cardsData, echartsNew },
  mounted() {
      this.tableForm.id = this.$route.params.id;
    this.getData();
    this.getList();
  },
  methods: {
    // 选择时间
    selectChange(tab) {
      this.tableForm.page = 1;
      this.tableForm.time = tab;
      this.timeVal = [];
      this.getData();
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.tableForm.time = this.timeVal[0] ? this.timeVal.join("-") : "";
      this.tableForm.page = 1;
      this.getData();
      this.getList();
    },
    getList() {
      this.userLoading = true;
      workGroupStatisticsList(this.tableForm)
        .then((res) => {
          this.tableData = res.data;
          this.userLoading = false;
        })
        .catch((err) => {
          this.userLoading = false;
          this.$Message.error(err.msg);
        });
    },
    getData() {
      workGroupStatistics({
          id:this.$route.params.id,
          time:this.tableForm.time
        }).then((res) => {
        this.cardLists = [
          {
            col: 6,
            count: res.data.toDaySum,
            name: "今日新增人数",
            type:1,
            className: "iconjinrixinzeng",
          },
          {
            col: 6,
            count: res.data.toDayReturn,
            name: "今日退群人数",
            type:1,
            className: "iconjinrituiqun",
          },
          {
            col: 6,
            count: res.data.groupChatSum,
            name: "当前群人数",
            type:1,
            className: "icondangqianqunchengyuan",
          },
          {
            col: 6,
            count: res.data.groupChatReturnSum,
            name: "当前退群人数",
            type:1,
            className: "iconleijituiqun",
          },
        ];
        let xAxis = new Array();
        let series = [
          {
            name: "入群",
            data: [],
            type: "line",
            smooth: "true",
            yAxisIndex: 0,
          },
          {
            name: "退群",
            data: [],
            type: "line",
            smooth: "true",
            yAxisIndex: 0,
          },
        ];
        if(res.data.groupChatReturnList.length > res.data.groupChatList.length ){
          xAxis = res.data.groupChatReturnList.map((item) => {
            return item.time;
          });
        }else{
           xAxis = res.data.groupChatList.map((item) => {
            return item.time;
          });
        }
        
        series[0].data = res.data.groupChatList.map((item) => {
          return item.sum;
        });
        series[1].data = res.data.groupChatReturnList.map((item) => {
          return item.sum;
        });
        this.spinShow = true;
        let legend = series.map((item) => {
          return item.name;
        });
        let col = ["#B37FEB", "#FFAB2B", "#1890FF", "#00C050"];
        series.map((item, index) => {
          item.itemStyle = {
            normal: {
              color: col[index],
            },
          };
        });
        this.optionData = {
          tooltip: {
            trigger: "axis",
            axisPointer: {
              type: "cross",
              label: {
                backgroundColor: "#6a7985",
              },
            },
          },
          legend: {
            x: "center",
            data: legend,
          },
          grid: {
            left: "3%",
            right: "4%",
            bottom: "3%",
            containLabel: true,
          },
          toolbox: {
            feature: {
              saveAsImage: {},
            },
          },
          xAxis: {
            type: "category",
            boundaryGap: true,
            axisLabel: {
              interval: 0,
              rotate: 40,
              textStyle: {
                color: "#000000",
              },
            },
            data: xAxis,
          },
          yAxis: [
            {
              type: "value",
              name: "数量",
              axisLine: {
                show: false,
              },
              axisTick: {
                show: false,
              },
              axisLabel: {
                textStyle: {
                  color: "#7F8B9C",
                },
              },
              splitLine: {
                show: true,
                lineStyle: {
                  color: "#F5F7F9",
                },
              },
            },
          ],
          series: series,
        };
        this.spinShow = false;
      });
    },
    pageChange(index) {
      this.tableForm.page = index;
      this.getList();
    },
  },
};
</script>
<style scoped lang="stylus">
.mb20 {
  margin-bottom: 20px;
}

.mb30 {
  margin-bottom: 30px;
}

.mr-20 {
  margin-right: 20px;
}

.header_title {
  font-size: 16px;
  font-family: PingFangSC-Semibold, PingFang SC;
  font-weight: 600;
  color: #000000;
  padding-left: 8px;
  position: relative;

  &::before {
    position: absolute;
    content: '';
    width: 2px;
    height: 18px;
    background: #1890ff;
    top: 0;
    left: 0;
  }
}
.font-sm{
  font-size: 12px;
}
</style>