<template>
  <div class="flex">
    <Modal
      v-model="memberStatus"
      width="680px"
      title="选择成员"
      :mask-closable="false"
      @on-ok="handleConfirm"
      @on-cancel="handleConcel"
      :z-index="9"
    >
      <div v-if="isShow" class="container">
        <Row>
          <Col :span="24" class="search-p">
            <Input
              v-model="filterText"
              placeholder="搜索成员"
              class="input-with-select"
              search
               @on-search="searchData"
            />
          </Col>
          <div ref="treeScoll" class="tree-box" @scroll="scroll">
            <el-tree
              ref="tree"
              v-if="!isAppoint"
              :data="treeData"
              :props="defaultProps"
              :highlight-current="true"
              :default-expand-all="true"
              :expand-on-click-node="false"
              :filter-node-method="filterNode"
            >
              <div slot-scope="{ node, data }" class="custom-tree-node">
                <span
                  class="flex-box"
                  @click="() => append(node, data)"
                >
                <!--  :class="{ active: data.isCheck }" -->
                  <Icon
                    type="ios-folder"
                    size="15"
                    color="#FFCA28"
                    class="tree-icon"
                  />
                  {{ node.label }}
                  <span>({{ data.member.length }})</span>
                </span>
                <div>
                  <div v-if="data.member.length" class="user-list">
                    <div
                      v-for="(user, index) in data.member"
                      :key="index"
                      class="user-item"
                      @click.stop="checkUser(user)"
                    >
                    <!--  :class="{ on: user.isCheck }" -->
                      <img
                        v-if="user.avatar !== ''"
                        :src="user.avatar"
                        alt=""
                      />
                      {{ user.name }}
                    </div>
                  </div>
                </div>
              </div>
            </el-tree>
          </div>
        </Row>
        <div class="select-box">
          <div class="title">已选择成员</div>
          <div class="list-box">
            <div
              v-for="(item, j) in checkUserList"
              :key="item.id"
              class="select-item"
            >
              <div class="left">
                <!-- <img v-if="item.avatar" :src="item.avatar" alt="" /> -->
                <span>{{ item.name }}</span>
              </div>
              <div class="right-box" v-if="!item.isDelete">
                <span
                  class="el-icon-close"
                  @click="handleDeleteUser(item, j)"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import { workTree } from "@/api/work";
export default {
  name: "Department",
  props: {
    isEdit: {
      type: Number,
      default: 0,
    },
    activeDepartment: {
      type: Object,
      default: () => {
        return null;
      },
    },
    userList: {
      type: Array,
      default: () => {
        return [];
      },
    },
    // 是否指定成员
    isAppoint: {
      type: Boolean,
      default: false,
    },
    // 指定成员数据 与 isAppoint 同时使用
    appointList: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      dialogVisible: false,
      treeData: [],
      selectList: {},
      isShow: false,
      memberStatus:false,
      checkUserList: [], // 选中人员列表
      scrollH: 0,
      filterText: "",
      openType:"",
      defaultProps: {
        children: "children",
        label: "name",
      },
    };
  },
  watch: {
    userList: {
      handler(nVal, oVal) {
        this.checkUserList = JSON.parse(JSON.stringify(nVal));
      },
      deep: true,
    },
    activeDepartment: {
      handler(nVal, oVal) {
        if (!nVal) return;
        this.isShow = false;
        let tempArr = [];
        const recursiveFunction = () => {
          const getStr = (list) => {
            tempArr = list.map((row, index, arr) => {
              if (row.children) {
                if (nVal.hasOwnProperty(row.id)) {
                  row.isCheck = true;
                } else {
                  row.isCheck = false;
                }
                getStr(row.children);
              } else {
                if (nVal.hasOwnProperty(row.id)) {
                  row.isCheck = true;
                } else {
                  row.isCheck = false;
                }
              }
              return arr;
            });
          };
          getStr(this.treeData);
        };
        recursiveFunction();
        this.$nextTick(() => {
          this.$set(this, "treeData", tempArr[0]);
          this.isShow = true;
          this.selectList = nVal;
        });
      },
      deep: true,
    },
    filterText(val) {
      this.$refs.tree.filter(val);
    },
    isAppoint: {
      handler(nVal) {
        console.log(this.appointList);
      },
      deep: true,
    },
  },
  mounted() {
    this.getTreeData();
  },
  methods: {
    getTreeData() {
      workTree().then((res) => {
        this.isShow = true;
        this.treeData = res.data;
        // this.getItemTree(this.treeData)
      });
    },
    handleClick(node, data) {
      this.isShow = false;
      this.$set(node, "isCheck", true);
      this.isShow = true;
      this.$set(this.selectList, node.id, node);
    },
    append(node, data) {
      this.$Message.warning("只允许添加成员");
      //   if (this.onlyPerson) return this.$message.warning(this.$t('public.waring'))
      //   if (this.onlyDepartment && Object.keys(this.selectList).length > 0) {
      //     return this.$message.warning(this.$t('setting.admin.deletetitle4'))
      //   }
      //   this.isShow = false
      //   data.isCheck = false
      //   data.is_mastart = false
      //   this.$set(this.selectList, data.id, data)
      //   this.$nextTick(() => {
      //     this.isShow = true
      //     this.$nextTick(() => {
      //       this.$refs.treeScoll.scroll(0, this.scrollH)
      //     })
      //   })
    },
    handleDelete(key) {
      let tempArr = [];
      this.isShow = false;
      delete this.selectList[key.id];
      var recursiveFunction = () => {
        const getStr = (list) => {
          tempArr = list.map((row, index, arr) => {
            if (row.children) {
              if (row.id === key.id) {
                row.isCheck = false;
              }
              getStr(row.children);
            } else {
              if (row.id === key.id) {
                row.isCheck = false;
              }
            }
            return arr;
          });
        };
        getStr(this.treeData);
      };
      recursiveFunction();
      this.$nextTick(() => {
        this.$set(this, "treeData", tempArr[0]);
        this.isShow = true;
      });
    },
    // 确认
    handleConfirm() {
        let data = this.checkUserList;
        this.$emit("changeMastart", data,this.openType);
        this.checkUserList = [];
    },
    // 重置树形数据状态
    reseatData() {
      this.isShow = false;
      let tempArr = [];
      const recursiveFunction = () => {
        const getStr = (list) => {
          tempArr = list.map((row, index, arr) => {
            row.isCheck = false;
            if (row.children) {
              row.user.forEach((user) => {
                user.isCheck = false;
              });
              getStr(row.children);
            } else {
              row.user.forEach((user) => {
                user.isCheck = false;
              });
            }
            return arr;
          });
        };
        getStr(this.treeData);
      };
      recursiveFunction();
      this.$nextTick(() => {
        this.checkUserList = JSON.parse(JSON.stringify(this.userList));
        this.$set(this, "treeData", tempArr[0]);
        this.isShow = true;
      });
    },
    scroll(e) {
      this.scrollH = e.target.scrollTop;
    },
    // 选中人员
    checkUser(user) {
      if (this.checkUserList.length === 0) {
        this.$set(user, "isCheck", true);
        this.checkUserList.push(user);
      } else {
        const status = this.checkUserList.some((el, index) => {
          return el.userid === user.userid
        })
        if (status) {
          return this.$Message.warning("该人员已选择");
        } else {
          this.$set(user, 'isCheck', true)
          this.checkUserList.push(user)
        }
      }
      this.$nextTick(() => {
        this.isShow = true;
        this.$nextTick(() => {
          this.$refs.treeScoll.scroll(0, this.scrollH);
        });
      });
    },
    // 删除人员
    handleDeleteUser(item, index) {
      this.checkUserList.splice(index, 1);
      var recursiveFunction = () => {
        const getStr = list => {
          list.forEach((row, index, arr) => {
            row.member.forEach(user => {
              if (user.id === item.id) {
                user.isCheck = false
              }
            })
          })
        }
        getStr(this.treeData)
      }
      recursiveFunction()
      this.$nextTick(() => {
        this.isShow = true
      })
    },
    filterNode(value, data, node) {
      // 如果什么都没填就直接返回
      if (!value) return true;
      // 如果传入的value和data中的label相同说明是匹配到了
      if (data.label.indexOf(value) !== -1) {
        return true;
      }
      // 否则要去判断它是不是选中节点的子节点
      return this.checkBelongToChooseNode(value, data, node);
    },
    checkBelongToChooseNode(value, data, node) {
      const level = node.level;
      // 如果传入的节点本身就是一级节点就不用校验了
      if (level === 1) {
        return false;
      }
      // 先取当前节点的父节点
      var parentData = node.parent;
      if (node.data.user.length > 0) {
        var list = node.data.user.map((val) => {
          if (val.card.name.indexOf(value) !== -1) {
            return true;
          } else {
            return false;
          }
        });
      }
      if (list === undefined) {
        return false;
      } else {
        for (let i = 0; i < list.length; i++) {
          if (list[i] === true) {
            return true;
          }
        }
      }
      // 遍历当前节点的父节点
      var index = 0;
      while (index < level - 1) {
        // 如果匹配到直接返回
        if (parentData.data.label.indexOf(value) !== -1) {
          return true;
        }
        // 否则的话再往上一层做匹配
        parentData = parentData.parent;
        index++;
      }
      // 没匹配到返回false
      return false;
    },
    getItemTree(data) {
      data &&
        data.forEach((item, index) => {
          if (item.user != undefined && item.user.length > 0) {
            item.user.forEach((value) => {
              value.label = value.card.name;
              value.children = value;
            });
          }
          if (item.children && item.children.length > 0) {
            this.getItemTree(item.children);
          }
        });
      return data;
    },
    searchData(){
      
    },
    handleConcel() {
      // this.memberStatus = false;
    },
  },
};
</script>

<style lang="stylus" scoped>
.search-p {
  padding-right: 20px;

  >>> .el-input-group__append {
    top: 0;
    background-color: transparent;
    border-left: none;
    text-align: center;
  }
}

::-webkit-scrollbar-thumb {
  -webkit-box-shadow: inset 0 0 6px #ddd;
  display: none;
}

.tree-box:hover::-webkit-scrollbar-thumb, .select-box:hover::-webkit-scrollbar-thumb {
  display: block;
}

::-webkit-scrollbar {
  width: 3px !important; /* 对垂直流动条有效 */
}

/deep/.el-dialog__body {
  height: 420px;
  padding: 5px 10px 15px 20px !important;
}

/deep/.el-tree-node:focus > .el-tree-node__content {
  background: unset !important;
}

/deep/.el-tree-node__expand-icon {
  margin-top: 9px;
}

/deep/.el-tree--highlight-current .el-tree-node.is-current > .el-tree-node__content {
  background: transparent !important;
}

/deep/.el-tree-node__content:hover {
  background: unset !important;
}

/deep/.el-tree-node__content:active {
  background: unset !important;
}

/deep/.flex-box:hover {
  background: #f5f7fa;
}

/deep/.el-tree-node__content {
  height: auto;
  align-items: start;
}

/deep/.el-tree--highlight-current .el-tree-node.is-current > .el-tree-node__content .flex-box {
  color: #1890ff;
}

/deep/.el-tree-node__content {
  padding-right: 15px;
  border-right: 2px solid transparent;
}

/deep/.el-tree-node > .el-tree-node__children {
  overflow: inherit;
}

.custom-tree-node {
  position: relative;
  width: 100%;

  .right-icon {
    display: none;
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
  }

  .edit-box {
    z-index: 200;
    position: absolute;
    right: 0;
    bottom: -124px;
    width: 120px;
    padding: 7px 0;
    box-shadow: 0px 0px 8px 0px rgba(0, 0, 0, 0.06);
    border-radius: 6px;
    color: #000000;
    background: #fff;

    .edit-item {
      height: 40px;
      line-height: 40px;
      padding-left: 19px;
      font-size: 13px;

      &:active {
        background: #f5f5f5;
      }
    }
  }
}

.tree-icon {
  margin-right: 5px;
  font-size: 18px;
  color: #ffca28;
}

.flex-box {
  display: flex;
  align-items: center;
  height: 40px;

  &.active {
    color: #1890ff;
  }
}

.user-list {
  padding-left: 25px;

  .user-item {
    display: flex;
    align-items: center;
    font-size: 14px;
    padding: 12px 0;

    &:hover {
      background: #f5f7fa;
    }

    img {
      width: 15px;
      height: 15px;
      margin-right: 7px;
      border-radius: 50%;
    }
  }
}

.container {
  display: flex;
  height: 100%;

  .tree-box {
    flex: 1;
    padding-right: 20px;
    overflow-y: auto;
    height: 100%;
  }

  .select-box {
    overflow-y: auto;
    height: 100%;
    width: 340px;
    padding-left: 20px;
    padding-top: 6px;
    border-left: 1px solid #eeeeee;
    padding-right: 10px;

    .title {
      color: rgba(0, 0, 0, 0.6);
      font-size: 13px;
    }

    .list-box {
      .select-item {
        display: flex;
        justify-content: space-between;
        margin-top: 18px;
        cursor: pointer;

        .left {
          display: flex;
        }

        img {
          width: 16px;
          height: 16px;
          margin-right: 5px;
          border-radius: 50%;
        }

        .el-icon-s-cooperation {
          margin-top: -2px;
        }

        .right-box {
          display: flex;
          align-items: center;
          justify-content: space-between;
          font-size: 12px;

          .el-icon-close {
            /* margin-left: 30px; */
            font-size: 16px;
          }
        }
      }
    }
  }
}

.on {
  color: #1890ff;
}
</style>