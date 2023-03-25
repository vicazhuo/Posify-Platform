<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div style="padding: 20px 20px 0">
        <Form
          ref="userFrom"
          :model="userFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <Row :gutter="24">
            <Col span="18">
              <Row>
                <Col span="24">
                  <Row>
                    <Col v-bind="grid">
                      <FormItem label="用户搜索：" label-for="nickname">
                        <Input
                          v-model="userFrom.nickname"
                          placeholder="请输入"
                          element-id="nickname"
                          clearable
                          style="max-width:250px;"
                        >
                          <Select
                            v-model="field_key"
                            slot="prepend"
                            style="width: 80px"
                          >
                            <Option value="all">全部</Option>
                            <Option value="uid">UID</Option>
                            <Option value="phone">手机号</Option>
                            <Option value="nickname">用户昵称</Option>
                          </Select>
                        </Input>
                      </FormItem>
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
            <template v-if="collapse">
              <Col span="18">
                <Row>
                  <Col v-bind="grid">
                    <FormItem label="用户分组：" label-for="group_id">
                      <Select
                        v-model="userFrom.group_id"
                        placeholder="请选择"
                        element-id="group_id"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option
                          :value="item.id"
                          v-for="(item, index) in groupList"
                          :key="index"
                          >{{ item.group_name }}</Option
                        >
                      </Select>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid">
                    <FormItem label="用户标签：" label-for="label_id">
                      <div
                        class="labelInput acea-row row-between-wrapper"
                        @click="openLabelList"
                      >
                        <div style="width:85%;">
                          <div v-if="dataLabel.length">
                            <Tag
                              closable
                              v-for="(item, index) in dataLabel"
                              :key="index"
                              @on-close="closeLabel(item)"
                              >{{ item.label_name }}</Tag
                            >
                          </div>
                          <span class="span" v-else>请选择</span>
                        </div>
                        <div class="iconfont iconxiayi"></div>
                      </div>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid">
                    <FormItem label="性别：" label-for="sex">
                      <Select
                        v-model="userFrom.sex"
                        placeholder="请选择"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option value="1">男</Option>
                        <Option value="2">女</Option>
                        <Option value="0">未知</Option>
                      </Select>
                    </FormItem>
                  </Col>
                </Row>
              </Col>
              <Col span="18">
                <Row>
                  <Col v-bind="grid">
                    <FormItem label="会员等级：" label-for="level">
                      <Select
                        v-model="userFrom.level"
                        placeholder="请选择"
                        element-id="level"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option
                          :value="item.id"
                          v-for="(item, index) in levelList"
                          :key="index"
                          >{{ item.name }}</Option
                        >
                      </Select>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid">
                    <FormItem label="付费会员：" label-for="isMember">
                      <Select
                        v-model="userFrom.isMember"
                        placeholder="请选择"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option value="1">是</Option>
                        <Option value="0">否</Option>
                      </Select>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid">
                    <FormItem label="身份：">
                      <Select
                        v-model="userFrom.is_promoter"
                        placeholder="请选择"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option value="1">推广员</Option>
                        <Option value="0">普通用户</Option>
                      </Select>
                    </FormItem>
                  </Col>
                </Row>
              </Col>

              <Col span="18">
                <Row>
                  <Col v-bind="grid">
                    <FormItem label="访问时间：" label-for="user_time">
                      <DatePicker
                        :editable="false"
                        @on-change="onchangeTime"
                        :value="timeVal"
                        format="yyyy/MM/dd"
                        type="daterange"
                        placement="bottom-start"
                        placeholder="自定义时间"
                        class="dateMedia"
                        :options="options"
                      ></DatePicker>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid">
                    <FormItem label="访问情况：" label-for="user_time_type">
                      <Select
                        v-model="userFrom.user_time_type"
                        placeholder="请选择"
                        element-id="user_time_type"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="all">全部</Option>
                        <Option value="visitno">时间段未访问</Option>
                        <Option value="visit">时间段访问过</Option>
                        <Option value="add_time">首次访问</Option>
                      </Select>
                    </FormItem>
                  </Col>
                   <Col v-bind="grid">
                    <FormItem label="下单次数：" label-for="pay_count">
                      <Select
                        v-model="userFrom.pay_count"
                        placeholder="请选择"
                        element-id="pay_count"
                        clearable
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option value="-1">0次</Option>
                        <Option value="0">1次以上</Option>
                        <Option value="1">2次以上</Option>
                        <Option value="2">3次以上</Option>
                        <Option value="3">4次以上</Option>
                        <Option value="4">5次以上</Option>
                      </Select>
                    </FormItem>
                  </Col>
                </Row>
              </Col>
              <Col span="18">
                <Row>
                  <Col v-bind="grid">
                    <FormItem label="国家：" label-for="country">
                      <Select
                        v-model="userFrom.country"
                        placeholder="请选择"
                        element-id="country"
                        clearable
                        @on-change="changeCountry"
                        style="max-width:250px;"
                      >
                        <Option value="">全部</Option>
                        <Option value="domestic">中国</Option>
                        <Option value="abroad">其他</Option>
                      </Select>
                    </FormItem>
                  </Col>
                  <Col v-bind="grid" v-if="userFrom.country === 'domestic'">
                    <FormItem label="省份：">
                      <Cascader
                        :data="addresData"
                        :value="address"
                        v-model="address"
                        @on-change="handleChange"
                        style="max-width:250px;"
                      ></Cascader>
                    </FormItem>
                  </Col>
                </Row>
              </Col>

            </template>
            <Col span="6" class="ivu-text-right userFrom">
              <FormItem>
                <Button
                  type="primary"
                  label="default"
                  class="mr15"
                  @click="userSearchs"
                  >搜索</Button
                >
                <Button class="ResetSearch" @click="reset('userFrom')"
                  >重置</Button
                >
                <a v-font="14" class="ivu-ml-8" @click="collapse = !collapse">
                  <template v-if="!collapse">
                    展开 <Icon type="ios-arrow-down" />
                  </template>
                  <template v-else>
                    收起 <Icon type="ios-arrow-up" />
                  </template>
                </a>
              </FormItem>
            </Col>
          </Row>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt listbox">
      <div class="new_tab">
        <Tabs @on-click="onClickTab">
          <TabPane
            :label="item.name"
            :name="item.type"
            v-for="(item, index) in headeNum"
            :key="index"
          />
        </Tabs>
      </div>
      <Row type="flex" justify="space-between">
        <Col span="24">
          <Button
            v-auth="['admin-user-save']"
            type="primary"
            class="mr20"
            @click="save"
            >添加用户</Button
          >
          <Tooltip
            content="本页至少选中一项"
            :disabled="!!selectionList.length"
          >
            <Button
              v-auth="['admin-user-coupon']"
              type="primary"
              class="mr20"
              :disabled="!selectionList.length"
              @click="onSend"
              >发送优惠券</Button
            >
          </Tooltip>
          <Button
            v-auth="['admin-wechat-news']"
            class="greens mr20"
            size="default"
            @click="onSendPic"
            v-if="userFrom.user_type === 'wechat'"
          >
            <Icon type="md-list"></Icon>
            发送图文消息
          </Button>
          <Tooltip
            content="本页至少选中一项"
            :disabled="!!selectionList.length"
          >
            <Button
              v-auth="['admin-user-group_set']"
              class="mr20"
              :disabled="!selectionList.length"
              @click="setGroup"
              >批量设置分组</Button
            >
          </Tooltip>
          <Tooltip
            content="本页至少选中一项"
            :disabled="!!selectionList.length"
          >
            <Button
              v-auth="['admin-user-set_label']"
              class="mr20"
              :disabled="!selectionList.length"
              @click="setLabel"
              >批量设置标签</Button
            >
          </Tooltip>
          <!--<Button v-auth="['admin-user-synchro']" class="mr20" @click="synchro"-->
          <!--&gt;同步公众号用户</Button-->
          <!--&gt;-->
        </Col>
        <!-- <Col span="24" class="userAlert" v-if="selectionList.length">
                    <Alert show-icon> 已选择<i class="userI"> {{selectionList.length}} </i>项</Alert>
                </Col> -->
      </Row>
      <Table
        :columns="columns"
        :data="userLists"
        class="mt25"
        ref="table"
        highlight-row
        :loading="loading"
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
        @on-selection-change="onSelectTab"
        @on-sort-change="sortChanged"
        @on-select-all="selectAll"
        @on-select-all-cancel="selectAll"
        @on-select-cancel="onSelectCancel"
      >
        <template slot-scope="{ row, index }" slot="avatars">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.avatar" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row, index }" slot="nickname">
          <div class="acea-row">
            <Icon
              type="md-male"
              v-show="row.sex === '男'"
              color="#2db7f5"
              size="15"
              class="mr5"
            />
            <Icon
              type="md-female"
              v-show="row.sex === '女'"
              color="#ed4014"
              size="15"
              class="mr5"
            />
            <div v-if="row.delete_time != null" style="color:#ed4014;">
							{{row.nickname}} (已注销)
						</div>
			<div v-else v-text="row.nickname"></div>
          </div>
          <!-- <div v-show="row.vip_name" class="vipName">{{row.vip_name}}</div> -->
        </template>
        <template slot-scope="{ row, index }" slot="isMember">
          <div>{{ row.isMember ? "是" : "否" }}</div>
        </template>
        <template slot-scope="{ row, index }" slot="follow_list">
          <div v-if="row.follow_list">
            <span v-for="(item, index1) in row.follow_list" :key="index1">{{
              item.member.name
            }}</span>
          </div>

          <!-- <div>{{ row.isMember ? "是" : "否" }}</div> -->
        </template>
        <!--                <template slot-scope="{ row, index }" slot="status">-->
        <!--                    <i-switch v-model="row.status" :value="row.status" :true-value="1" :false-value="0" @on-change="onchangeIsShow(row)" size="large">-->
        <!--                        <span slot="open">显示</span>-->
        <!--                        <span slot="close">隐藏</span>-->
        <!--                    </i-switch>-->
        <!--                </template>-->
        <template slot-scope="{ row, index }" slot="action">
		  <span v-if="row.delete_time != null" style="color: #c5c8ce;">编辑</span>
          <a v-else @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click="changeMenu(row, '1')">详情</a>
          <!-- <template>
				    本身就没有等级
            <Dropdown @on-click="changeMenu(row, $event, index)">
              <a href="javascript:void(0)">
                更多
                <Icon type="ios-arrow-down"></Icon>
              </a>
              <DropdownMenu slot="list">
              <DropdownItem name="1">用户详情</DropdownItem>
                <DropdownItem name="2">积分余额</DropdownItem>
                <DropdownItem name="3">赠送会员</DropdownItem>
                <DropdownItem name="4" v-if="row.vip_name">清除等级</DropdownItem>
                <DropdownItem name="5">设置分组</DropdownItem>
                <DropdownItem name="6">设置标签</DropdownItem>
                <DropdownItem name="7">修改上级推广人</DropdownItem>
                <DropdownItem name="8" v-if="row.spread_uid"
                  >清除上级推广人</DropdownItem
                >
              </DropdownMenu>
            </Dropdown>
          </template> -->
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="userFrom.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="userFrom.limit"
        />
      </div>
    </Card>
    <!-- 用户标签 -->
    <Modal
      v-model="labelListShow"
      scrollable
      title="请选择用户标签"
      :closable="false"
      width="320"
      :footer-hide="true"
      :mask-closable="false"
    >
      <labelList
        ref="labelList"
        @activeData="activeData"
        @close="labelListClose"
      ></labelList>
    </Modal>
    <!-- 编辑表单 积分余额-->
    <edit-from
      ref="edits"
      :FromData="FromData"
	  :userEdit="1"
      @submitFail="submitFail"
    ></edit-from>
    <!-- 发送优惠券-->
    <send-from
      ref="sends"
      :is-all="isAll"
      :where="userFrom"
      :userIds="user_ids"
    ></send-from>
    <!-- 会员详情-->
    <user-details ref="userDetails" :group-list="groupList"></user-details>
    <!--发送图文消息 -->
    <Modal
      v-model="modal13"
      scrollable
      title="发送消息"
      width="1200"
      height="800"
      footer-hide
      class="modelBox"
    >
      <news-category
        v-if="modal13"
        :isShowSend="isShowSend"
        :userIds="user_ids"
        :scrollerHeight="scrollerHeight"
        :contentTop="contentTop"
        :contentWidth="contentWidth"
        :maxCols="maxCols"
      ></news-category>
    </Modal>
    <!--修改推广人-->
    <Modal
      v-model="promoterShow"
      scrollable
      title="修改推广人"
      class="order_box"
      :closable="false"
    >
      <Form
        ref="formInline"
        :model="formInline"
        :label-width="100"
        @submit.native.prevent
      >
        <FormItem label="用户头像：" prop="image">
          <div class="picBox" @click="customer">
            <div class="pictrue" v-if="formInline.image">
              <img v-lazy="formInline.image" />
            </div>
            <div class="upLoad acea-row row-center-wrapper" v-else>
              <Icon type="ios-camera-outline" size="26" />
            </div>
          </div>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="putSend('formInline')">提交</Button>
        <Button @click="cancel('formInline')">取消</Button>
      </div>
    </Modal>
    <Modal
      v-model="customerShow"
      scrollable
      title="请选择商城用户"
      :closable="false"
      width="50%"
    >
      <customerInfo
        v-if="customerShow"
        @imageObject="imageObject"
      ></customerInfo>
    </Modal>
    <Modal
      v-model="labelShow"
      scrollable
      title="请选择用户标签"
      :closable="false"
      width="320"
      :footer-hide="true"
    >
      <userLabel :uid="labelActive.uid" @close="labelClose"></userLabel>
    </Modal>
  </div>
</template>

<script>
import userLabel from "../../../components/userLabel";
import labelList from "@/components/labelList";
import { mapState } from "vuex";
import expandRow from "./tableExpand.vue";
import {
  userList,
  getUserData,
  isShowApi,
  editOtherApi,
  giveLevelApi,
  userSetGroup,
  userGroupApi,
  levelListApi,
  userSetLabelApi,
  userLabelApi,
  userSynchro,
  getUserSaveForm,
  giveLevelTimeApi,
} from "@/api/user";
import { agentSpreadApi } from "@/api/agent";
import editFrom from "../../../components/from/from";
import sendFrom from "@/components/sendCoupons/index";
import userDetails from "./handle/userDetails";
import newsCategory from "@/components/newsCategory/index";
import city from "@/utils/city";
import customerInfo from "@/components/customerInfo";
export default {
  name: "user_list",
  components: {
    expandRow,
    editFrom,
    sendFrom,
    userDetails,
    newsCategory,
    customerInfo,
    userLabel,
    labelList,
  },
  data() {
    return {
      dataLabel: [],
      labelListShow: false,
      labelShow: false,
      customerShow: false,
      promoterShow: false,
      labelActive: {
        uid: 0,
      },
      formInline: {
        uid: 0,
        spread_uid: 0,
        image: "",
      },
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
      collapse: false,
      headeNum: [
        { type: "", name: "全部" },
        { type: "wechat", name: "微信公众号" },
        { type: "routine", name: "微信小程序" },
        { type: "h5", name: "H5" },
        { type: "pc", name: "PC" },
        { type: "app", name: "APP" },
      ],
      address: [],
      addresData: city,
      isShowSend: true,
      modal13: false,
      maxCols: 4,
      scrollerHeight: "600",
      contentTop: "130",
      contentWidth: "98%",
      grid: {
        xl: 8,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      grid2: {
        xl: 18,
        lg: 16,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      total: 0,
      userFrom: {
        label_id: "",
        user_type: "",
        status: "",
        sex: "",
        is_promoter: "",
        country: "",
        isMember: "",
        pay_count: "",
        user_time_type: "",
        user_time: "",
        nickname: "",
        province: "",
        city: "",
        page: 1,
        limit: 15,
        level: "",
        group_id: "",
        field_key: "",
      },
      field_key: "",
      level: "",
      group_id: "",
      label_id: "",
      user_time_type: "",
      pay_count: "",
      columns: [
        {
          type: "expand",
          width: 40,
          render: (h, params) => {
            return h(expandRow, {
              props: {
                row: params.row,
              },
            });
          },
        },
        // {
        //     type: 'selection',
        //     width: 60,
        //     align: 'center'
        // },
        {
          width: 60,
          align: "center",
          renderHeader: (h, params) => {
            return h(
              "div",
              {
                class: {
                  "select-panel": true,
                },
                on: {
                  mouseenter: (e) => {
                    this.display = "block";
                  },
                  mouseleave: (e) => {
                    this.display = "none";
                  },
                },
              },
              [
                h("Checkbox", {
                  props: {
                    value: this.checkBox,
                  },
                  on: {
                    "on-change": (e) => {
                      this.checkBox = e;
                      this.$refs.table.selectAll(this.checkBox);
                      this.isAll = e ? 0 : -1;
                    },
                  },
                }),
                h(
                  "div",
                  {
                    style: {
                      position: "absolute",
                      top: 0,
                      zIndex: 2,
                      display: this.display,
                      width: "80px",
                      // height: "100%",
                      padding: "5px",
                      borderRadius: "4px",
                      backgroundColor: "#fff",
                      boxShadow: "0 0px 5px rgba(0, 0, 0, 0.2)",
                      transform: "translateX(25%)",
                    },
                  },
                  [
                    h(
                      "div",
                      {
                        class: {
                          "select-item": true,
                          on: this.isAll === 0,
                        },
                        style: {
                          padding: "1px 6px",
                          cursor: "pointer",
                          height: "50%",
                        },
                        on: {
                          click: (e) => {
                            if (this.isAll === 0) {
                              this.isAll = -1;
                              this.checkBox = false;
                              this.$refs.table.selectAll(this.checkBox);
                            } else {
                              this.isAll = 0;
                              if (!this.selectionList.length) {
                                this.checkBox = true;
                                this.$refs.table.selectAll(this.checkBox);
                              }
                            }
                            this.display = "none";
                          },
                        },
                      }
                      // "选择当页"
                    ),
                    h(
                      "div",
                      {
                        class: {
                          "select-item": true,
                          on: this.isAll === 1,
                        },
                        style: {
                          padding: "4px",
                          cursor: "pointer",
                          // height: "50%",
                        },
                        on: {
                          click: (e) => {
                            if (this.isAll === 1) {
                              this.isAll = -1;
                              this.checkBox = false;
                            } else {
                              this.isAll = 1;
                              this.checkBox = true;
                            }
                            this.$refs.table.selectAll(this.checkBox);
                            this.display = "none";
                          },
                        },
                      },
                      "选择全部"
                    ),
                  ]
                ),
              ]
            );
          },
          render: (h, params) => {
            return h("Checkbox", {
              props: {
                value: params.row.checkBox,
              },
              on: {
                "on-change": (e) => {
                  if (e) {
                    this.selectionList.push(params.row);
                  } else {
                    this.checkBox = false;
                    this.selectionList.forEach((item, index) => {
                      if (item.id === params.row.id) {
                        this.selectionList.splice(index, 1);
                      }
                    });
                  }
                  this.isAll = this.selectionList.length ? 0 : -1;
                  // this.responseData为查询出的结果数据
                  // params.row.checkBox = e;
                  params.row.checkBox = e;
                  this.userLists[params.index].checkBox = e;
                },
              },
              ref: "checkbox",
              refInFor: true,
            });
          },
        },
        {
          title: "UID",
          key: "uid",
          width: 60,
        },
        {
          title: "头像",
          slot: "avatars",
          width: 50,
        },
        {
          title: "昵称",
          slot: "nickname",
          minWidth: 150,
        },
        {
          title: "付费会员",
          slot: "isMember",
          minWidth: 90,
        },
        {
          title: "用户等级",
          key: "level",
          minWidth: 90,
        },
        {
          title: "分组",
          key: "group_id",
          minWidth: 100,
        },
        {
          title: "手机号",
          key: "phone",
          minWidth: 100,
        },
        {
          title: "用户类型",
          key: "user_type",
          minWidth: 100,
        },
        {
          title: "内部联系人",
          slot: "follow_list",
          minWidth: 100,
        },
        {
          title: "余额",
          key: "now_money",
          sortable: "custom",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
        },
      ],
      userLists: [],
      FromData: null,
      selectionList: [],
      user_ids: "",
      selectedData: [],
      timeVal: [],
      array_ids: [],
      groupList: [],
      levelList: [],
      labelFrom: {
        page: 1,
        limit: "",
      },
      labelLists: [],
      display: "none",
      checkBox: false,
      selectionCopy: [],
      isAll: -1,
      userId: 0,
    };
  },
  watch: {
    selectionList(value) {
      let arr = value.map((item) => item.uid);
      this.array_ids = arr;
      this.user_ids = arr.join();
    },
    userLists: {
      deep: true,
      handler(value) {
        value.forEach((item) => {
          this.selectionList.forEach((itm) => {
            if (itm.uid === item.uid) {
              item.checkBox = true;
            }
          });
        });
        const arr = this.userLists.filter((item) => item.checkBox);
        if (this.userLists.length) {
          this.checkBox = this.userLists.length === arr.length;
        } else {
          this.checkBox = false;
        }
      },
    },
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 100;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    this.getList();
  },
  mounted() {
    this.userGroup();
    this.levelLists();
    this.groupLists();
  },
  methods: {
    closeLabel(label) {
      let index = this.dataLabel.indexOf(
        this.dataLabel.filter((d) => d.id == label.id)[0]
      );
      this.dataLabel.splice(index, 1);
    },
    activeData(dataLabel) {
      this.labelListShow = false;
      this.dataLabel = dataLabel;
    },
    openLabelList(row) {
      this.labelListShow = true;
      this.$refs.labelList.userLabel(
        JSON.parse(JSON.stringify(this.dataLabel))
      );
    },
    // 标签弹窗关闭
    labelListClose() {
      this.labelListShow = false;
    },

    selectAll(row) {
      if (row.length) {
        this.selectionList = row;
        this.selectionCopy = row;
      }
      this.selectionCopy.forEach((item, index) => {
        item.checkBox = this.checkBox;
        this.$set(this.userLists, index, item);
      });
    },
    // 标签弹窗关闭
    labelClose(e) {
      if (!e) {
        this.getList();
      }
      this.labelShow = false;
      this.labelActive.uid = 0;
    },
    // 提交
    putSend(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          if (!this.formInline.spread_uid) {
            return this.$Message.error("请上传用户");
          }
          agentSpreadApi(this.formInline)
            .then((res) => {
              this.promoterShow = false;
              this.$Message.success(res.msg);
              this.getList();
              this.$refs[name].resetFields();
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
        }
      });
    },
    save() {
      this.$modalForm(getUserSaveForm()).then(() => this.getList());
			// getUserSaveForm().then(async (res) => {
			// 	if(res.data.status === false){
			// 		return this.$authLapse(res.data);
			// 	}
			// 	this.FromData = res.data;
			// 	this.$refs.edits.modals = true;
			// }).catch(err=>{
			// 	this.$Message.error(err.msg);
			// })
    },
    synchro() {
      userSynchro()
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 分组列表
    groupLists() {
      this.loading = true;
      userLabelApi(this.labelFrom)
        .then(async (res) => {
          let data = res.data;
          this.labelLists = data.list;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    onClickTab(type) {
      this.userFrom.page = 1;
      this.userFrom.user_type = type;
      this.getList();
    },
    userGroup() {
      let data = {
        page: 1,
        limit: "",
      };
      userGroupApi(data).then((res) => {
        this.groupList = res.data.list;
      });
    },
    levelLists() {
      let data = {
        page: 1,
        limit: "",
        title: "",
        is_show: 1,
      };
      levelListApi(data).then((res) => {
        this.levelList = res.data.list;
      });
    },
    // 批量设置分组；
    setGroup() {
      if (this.selectionList.length === 0) {
        this.$Message.warning("请选择要设置分组的用户");
      } else {
        let uids = { all: this.isAll };
        if (this.isAll === 0) {
          uids.uids = this.array_ids;
        } else if (this.isAll === 1) {
          uids.where = this.userFrom;
          uids.where = {
            city: this.userFrom.city,
            country: this.userFrom.country,
            field_key: this.userFrom.field_key,
            group_id: this.userFrom.group_id,
            isMember: this.userFrom.isMember,
            is_promoter: this.userFrom.is_promoter,
            label_id: this.userFrom.label_id,
            level: this.userFrom.level,
            nickname: this.userFrom.nickname,
            pay_count: this.userFrom.pay_count,
            province: this.userFrom.province,
            sex: this.userFrom.sex,
            status: this.userFrom.status,
            user_time: this.userFrom.user_time,
            user_time_type: this.userFrom.user_time_type,
            user_type: this.userFrom.user_type,
          };
        }
        this.$modalForm(userSetGroup(uids)).then(() => this.getList());
      }
    },
    // 批量设置标签；
    setLabel() {
      if (this.selectionList.length === 0) {
        this.$Message.warning("请选择要设置标签的用户");
      } else {
        let uids = { all: this.isAll };
        if (this.isAll === 0) {
          uids.uids = this.array_ids;
        } else if (this.isAll === 1) {
          uids.where = {
            city: this.userFrom.city,
            country: this.userFrom.country,
            field_key: this.userFrom.field_key,
            group_id: this.userFrom.group_id,
            isMember: this.userFrom.isMember,
            is_promoter: this.userFrom.is_promoter,
            label_id: this.userFrom.label_id,
            level: this.userFrom.level,
            nickname: this.userFrom.nickname,
            pay_count: this.userFrom.pay_count,
            province: this.userFrom.province,
            sex: this.userFrom.sex,
            status: this.userFrom.status,
            user_time: this.userFrom.user_time,
            user_time_type: this.userFrom.user_time_type,
            user_type: this.userFrom.user_type,
          };
        }
        this.labelShow = true;
        this.labelActive.uid = uids;
        // this.$modalForm(userSetLabelApi(uids)).then(() => this.getList());
      }
    },
    // 是否为付费会员；
    changeMember() {
      this.userFrom.page = 1;
      this.getList();
    },
    // 选择国家
    changeCountry() {
      if (this.userFrom.country === "abroad" || !this.userFrom.country) {
        this.selectedData = [];
        this.userFrom.province = "";
        this.userFrom.city = "";
        this.address = [];
      }
    },
    // 选择地址
    handleChange(value, selectedData) {
      this.selectedData = selectedData.map((o) => o.label);
      this.userFrom.province = this.selectedData[0];
      this.userFrom.city = this.selectedData[1];
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.userFrom.user_time = this.timeVal.join("-");
    },
    // 操作
    changeMenu(row, name, index) {
      this.userId = row.uid;
      let uid = [];
      uid.push(row.uid);
      let uids = { uids: uid };
      switch (name) {
        case "1":
          this.$refs.userDetails.modals = true;
          this.$refs.userDetails.activeName = "info";
          this.$refs.userDetails.getDetails(row.uid);
          break;
        case "2":
          this.getOtherFrom(row.uid);
          break;
        case "3":
          // this.giveLevel(row.uid);
          this.giveLevelTime(row.uid);
          break;
        case "4":
          this.del(
            row,
            "清除 【 " + row.nickname + " 】的会员等级",
            index,
            "user"
          );
          break;
        case "5":
          this.$modalForm(userSetGroup(uids)).then(() =>
            this.$refs.sends.getList()
          );
          break;
        case "6":
          this.openLabel(row);
          // this.$modalForm(userSetLabelApi(uids)).then(() => this.$refs.sends.getList());
          break;
        case "7":
          this.editS(row);
          break;
        default:
          this.del(
            row,
            "解除【 " + row.nickname + " 】的上级推广人",
            index,
            "tuiguang"
          );
          break;
        // this.del(row, '清除 【 ' + row.nickname + ' 】的会员等级', index)
      }
    },
    openLabel(row) {
      this.labelShow = true;
      this.labelActive.uid = row.uid;
    },
    editS(row) {
      this.promoterShow = true;
      this.formInline.uid = row.uid;
    },
    customer() {
      this.customerShow = true;
    },
    imageObject(e) {
      this.customerShow = false;
      this.formInline.spread_uid = e.uid;
      this.formInline.image = e.image;
    },
    cancel(name) {
      this.promoterShow = false;
      this.$refs[name].resetFields();
    },
    // 赠送会员等级
    giveLevel(id) {
      giveLevelApi(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 赠送会员等级
    giveLevelTime(id) {
      giveLevelTimeApi(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 删除
    del(row, tit, num, name) {
      let delfromData = {
        title: tit,
        num: num,
        url:
          name === "user"
            ? `user/del_level/${row.uid}`
            : `agent/stair/delete_spread/${row.uid}`,
        method: name === "user" ? "DELETE" : "PUT",
        // url: `user/del_level/${row.uid}`,
        // method: 'DELETE',
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 清除会员删除成功
    submitModel() {
      this.getList();
    },
    // 会员列表
    getList() {
      this.loading = true;
      let activeIds = [];
      this.dataLabel.forEach((item) => {
        activeIds.push(item.id);
      });
      this.userFrom.label_ids = activeIds.join(",") || "";
      this.userFrom.user_type = this.userFrom.user_type || "";
      this.userFrom.status = this.userFrom.status || "";
      this.userFrom.sex = this.userFrom.sex || "";
      this.userFrom.is_promoter = this.userFrom.is_promoter || "";
      this.userFrom.country = this.userFrom.country || "";
      this.userFrom.user_time_type = this.userFrom.user_time_type || "";
      this.userFrom.pay_count = this.userFrom.pay_count || "";
      // this.userFrom.label_id = this.userFrom.label_id || "";
      this.userFrom.field_key = this.field_key === "all" ? "" : this.field_key;
      this.userFrom.level =
        this.userFrom.level === "all" ? "" : this.userFrom.level;
      this.userFrom.group_id =
        this.userFrom.group_id === "all" ? "" : this.userFrom.group_id;
      userList(this.userFrom)
        .then(async (res) => {
          let data = res.data;
          data.list.forEach((item) => {
            item.checkBox = false;
          });
          this.userLists = data.list;
          // this.userLists = data.list.map((item) => {
          //   item.checkBox = true;
          //   return item;
          // });
          if (this.isAll == 1) {
            // this.checkBox = true
            this.userLists = data.list.map((item) => {
              item.checkBox = true;
              return item;
            });
          }
          this.total = data.count;
          // this.isAll = -1;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    pageChange(index) {
      this.userFrom.page = index;
      this.getList();
    },
    // 搜索
    userSearchs() {
      if (this.userFrom.user_time_type && !this.timeVal.length) {
        return this.$Message.error("请选择访问时间");
      }
      if (this.timeVal.length && !this.userFrom.user_time_type) {
        return this.$Message.error("请选择访问情况");
      }
      this.userFrom.page = 1;
      this.selectionList = [];
      this.getList();
    },
    // 重置
    reset(name) {
      this.userFrom = {
        user_type: "",
        status: "",
        sex: "",
        is_promoter: "",
        country: "",
        pay_count: "",
        user_time_type: "",
        user_time: "",
        nickname: "",
        field_key: "",
        level: "",
        group_id: "",
        label_id: "",
        page: 1, // 当前页
        limit: 20, // 每页显示条数
      };
      this.field_key = "";
      this.level = "";
      this.group_id = "";
      this.label_id = "";
      this.user_time_type = "";
      this.pay_count = "";
      this.timeVal = [];
      this.selectionList = [];
      this.getList();
    },
    // 获取编辑表单数据
    getUserFrom(id) {
    this.$modalForm(getUserData(id)).then(() => this.getList());
      // getUserData(id)
      //   .then(async (res) => {
      //     if (res.data.status === false) {
      //       return this.$authLapse(res.data);
      //     }
      //     this.FromData = res.data;
      //     this.$refs.edits.modals = true;
      //   })
      //   .catch((res) => {
      //     this.$Message.error(res.msg);
      //   });
    },
    // 获取积分余额表单
    getOtherFrom(id) {
      editOtherApi(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          res.data.rules[1].props.max = 999999;
          res.data.rules[1].props.precision = 0;
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 修改状态
    onchangeIsShow(row) {
      let data = {
        id: row.uid,
        status: row.status,
      };
      isShowApi(data)
        .then(async (res) => {
          this.$Message.success(res.msg);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 全选
    onSelectTab(selection) {
      this.selectionList = selection;
      let data = [];
      this.selectionList.map((item) => {
        data.push(item.uid);
      });
      this.array_ids = data;
      this.user_ids = data.join(",");
    },
    // 点击发送优惠券
    onSend() {
      if (this.selectionList.length === 0) {
        this.$Message.warning("请选择要发送优惠券的用户");
      } else {
        this.$refs.sends.modals = true;
        this.$refs.sends.getList();
      }
    },
    // 发送图文消息
    onSendPic() {
      if (this.selectionList.length === 0) {
        this.$Message.warning("请选择要发送图文消息的用户");
      } else {
        this.modal13 = true;
      }
    },
    // 编辑
    edit(row) {
      this.getUserFrom(row.uid);
      // this.$modalForm(getUserSaveForm(row.uid)).then(() => this.getList());
    },
    // 修改成功
    submitFail(p) {
      this.getList();
      if (this.$refs.userDetails.modals) {
        this.$refs.userDetails.getDetails(this.userId);
      }
    },
    // 排序
    sortChanged(e) {
      this.userFrom[e.key] = e.order;
      this.getList();
    },
    onSelectCancel(selection, row) {},
  },
};
</script>

<style scoped lang="stylus">
.labelInput {
  max-width:250px;
  border: 1px solid #dcdee2;
  padding: 0 5px;
  border-radius: 5px;
  min-height: 30px;
  cursor: pointer;

  .span {
    color: #c5c8ce;
  }

  .iconxiayi {
    font-size: 12px;
  }
}

.picBox {
  display: inline-block;
  cursor: pointer;

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }
}

.userFrom {
  >>> .ivu-form-item-content {
    margin-left: 0px !important;
  }
}

.userAlert {
  margin-top: 20px;
}

.userI {
  color: #1890FF;
  font-style: normal;
}

img {
  height: 36px;
  display: block;
}

.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}

.tabBox_tit {
  width: 60%;
  font-size: 12px !important;
  margin: 0 2px 0 10px;
  letter-spacing: 1px;
  padding: 5px 0;
  box-sizing: border-box;
}

.modelBox {
  >>> .ivu-modal-body {
    padding: 0 16px 16px 16px !important;
  }
}

.vipName {
  color: #dab176;
}

.listbox {
  >>>.ivu-divider-horizontal {
    margin: 0 !important;
  }
}

/deep/.ivu-table-header {
  // overflow visible
}

/deep/.ivu-table th {
  overflow: visible;
}

/deep/.select-item:hover {
  background-color: #f3f3f3;
}

/deep/.select-on {
  display: block;
}

/deep/.select-item.on {
  /* background: #f3f3f3; */
}
.pane_pd{
  padding:4px 16px 20px !important;
  font-weight: 500;
}
.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab{
      padding:4px 16px 20px !important;
      font-weight: 500;
  }
}
.dateMedia{
  width: 250px;
  @media screen and (max-width: 1400px) {
    width: 250px;
  }
  @media screen and (max-width: 1200px) {
    width: 200px;
  }
  @media screen and (max-width: 900px) {
    width: 250px;
  }
}
</style>
