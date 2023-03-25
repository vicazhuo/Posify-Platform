<template>
	<Modal v-model="modals"  scrollable title="库存管理" width="800" 
	 @on-cancel="cancel" footer-hide
	 class-name="vertical-center-modal">
	    <Card :bordered="false" dis-hover class="cards">
					<div class="batch" v-if="specType">
						<div class="name" @click="batchTap">批量<span class="iconfont iconxiayi"></span></div>
						<div class="input acea-row row-center-wrapper" v-if="batchShow">
							  <Input v-model="batchStock" type='number' style="width: 150px;" @on-change="inputTap">
								<Select v-model="batchPm" slot="append" style="width: 60px" @on-change="batchStockTap">
								   <Option :value="1">入库</Option>
								   <Option :value="0">出库</Option>
								</Select>
							</Input>
						</div>
					</div>
	        <Table :columns="columns" border ref="selection" :data="stockData" :loading="loading" no-data-text="暂无数据"
	               highlight-row no-filtered-data-text="暂无筛选结果" max-height="450">
								 <template slot-scope="{ row, index }" slot="image">
								 	<div class="product-data">
								 		<img class="image" :src="row.image" />
								 	</div>
								 </template>
								 <template slot-scope="{ row, index }" slot="num">
									 <div class="acea-row row-middle">
									 <Input v-model="row.changeNum" type="number" style="width: 150px;" @on-change="changeTap(row)">
										 	<Select v-model="row.pm" slot="append" style="width: 60px" @on-change="stockTap(row)">
										 	   <Option :value="1">入库</Option>
										 	   <Option :value="0">出库</Option>
										 	</Select>
										 </Input>
										 <span class="ml20">={{row.resultNum}}</span>
									 </div>
								 </template>
					</Table>
					<div class="footer acea-row row-right">
						<Button class="mr" @click="cancel">取消</Button>
						<Button type="primary" @click="productSaveStocks">提交</Button>
					</div>
	    </Card>
	</Modal>
</template>

<script>
	  import { productAttrsApi, productSaveStocksApi } from '@/api/product.js'
    export default {
        name: 'stockEdit',
				props: {
				},
        data () {
            return {
							id:0,
							specType:0,
							batchShow:false,
							batchStock:0,
							batchPm:1,
							modals: false,
							loading: false,
							stockData:[],
							columns: [
									{
										title: "图片",
										slot: "image",
										minWidth: 20
									},
									{
										title: "产品规格",
										key: "suk",
										minWidth: 90
									},
									{
										title: "商品条形码",
										key: "bar_code",
										minWidth: 35
									},
									{
										title: "商品编码",
										key: "code",
										minWidth: 35
									},
									{
										title: "当前库存",
										key: "stock",
										minWidth: 10
									},
									{
										title: "入/出库数量",
										slot: "num",
										minWidth: 200
									}
								]
            }
        },
        methods: {
					// 批量设置；
					countBatch(){
						this.batchStock = Math.abs(this.batchStock);
						this.stockData.forEach(item=>{
							item.changeNum = this.batchStock;
							if(this.batchPm){
								item.pm = 1;
								item.resultNum = parseInt(item.stock) + parseInt(item.changeNum);
							}else{
								item.pm = 0;
								if(parseInt(item.stock)<=0){
									item.resultNum = 0
								}else{
									let num = parseInt(item.stock) - parseInt(item.changeNum)
									item.resultNum = num>=0?num:0;
								}
							}
						})
					},
					// 批量加减库存
					inputTap(){
						this.batchStock = this.batchStock.replace(/^\d{10}$/g,"0")
						this.countBatch();
					},
					// 批量设置入库或是出库
					batchStockTap(){
						this.countBatch();
					},
					// 单个设置
					countStock(row){
						if(row.pm){
							row.resultNum = (parseInt(row.stock) + parseInt(row.changeNum)) || row.stock;
						}else{
							if(parseInt(row.stock)<=0){
								row.resultNum = 0;
							}else{
								let num = (parseInt(row.stock) - parseInt(row.changeNum)) || row.stock
								row.resultNum = num>=0?num:0;
							}
						}
						this.stockData.forEach(item=>{
							if(row.id==item.id){
								item.changeNum = row.changeNum;
								item.resultNum = row.resultNum;
								item.pm = row.pm;
							}
						})
					},
					// 设置加减库存
					stockTap(row){
						this.countStock(row);
					},
					// 设置入库或是出库
					changeTap(row){
						row.changeNum = row.changeNum.replace(/^\d{10}$/g,"0")
						this.countStock(row);
					},
					batchTap(){
						this.batchShow = !this.batchShow;
					},
					productAttrs(data){
						this.specType = data.spec_type;
						this.id = data.id;
						productAttrsApi(data.id).then(res=>{
							let data = res.data;
							data.forEach(item=>{
								item.resultNum = item.stock;
								item.changeNum = 0;
								item.pm = 1;
							})
							this.stockData = data;
						}).catch(err=>{
							this.$Message.error(err.msg);
						})
					},
					productSaveStocks(){
						let attrs = [];
						this.stockData.forEach(item=>{
							let data = {
								"unique":item.unique,
								"pm":item.pm,
								"stock":item.changeNum
							}
							attrs.push(data)
						})
						productSaveStocksApi({attrs:attrs},this.id).then(res=>{
							this.$Message.success('修改成功');
							this.cancel();
							this.$emit('stockChange',res.data.stock)
						}).catch(err=>{
							this.$Message.error(err.msg);
						})
					},
					cancel(){
						this.modals = false;
						this.batchShow = false;
						this.batchPm = 1;
						this.batchStock = 0;
					}
        }
    }
</script>

<style lang="stylus" scoped>
	
	/*定义滑块 内阴影+圆角*/
	/deep/::-webkit-scrollbar-thumb{
	  -webkit-box-shadow: inset 0 0 6px #999;
	}
	/deep/::-webkit-scrollbar {
	    width: 4px!important; /*对垂直流动条有效*/
	}
	.footer{
		margin-top 20px;
	}
	.product-data .image {
		width: 50px !important;
		height: 50px !important;
	}
	.cards{
		position relative;
	}
	.batch{
		position absolute;
		right -20px;
		z-index 9;
		top:18px;
		width: 176px;
		.input{
			width: 176px;
			height: 56px;
			background: #FFFFFF;
			box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.05);
			border-radius: 4px;
			margin-left -80px;
		}
		.name{
			font-size 13px;
			color: #1890FF;
			margin-bottom 10px;
			cursor pointer;
			.iconfont{
				font-size 12px;
				margin-left 4px;
			}
		}
	}
	/deep/.ivu-select-single .ivu-select-selection .ivu-select-placeholder,
	/deep/.ivu-select-single .ivu-select-selection .ivu-select-selected-value{
		font-size 13px!important;
	}
	/deep/.ivu-card-body{
		padding 0!important;
	}
	/deep/.ivu-input:focus{
		border-color #dcdee2!important;
		box-shadow unset!important;
	}
	/deep/.ivu-input:hover{
		border-color #dcdee2!important;
	}
	/deep/.ivu-input{
		border-right 0 !important;
		transition:unset!important;
	}
	/deep/.ivu-input-group-append{
		background-color #fff !important;
	}
	/deep/.ivu-table{
		overflow unset!important;
	}
	/deep/.ivu-table-cell{
		overflow unset!important;
		font-size 13px!important;
	}
	/deep/.ivu-table-border:after{
		background-color #fff;
	}
	/deep/.ivu-table-header table{
		border-top: 0!important;
	}
	/deep/.ivu-modal-body{
		padding-top 0!important;
	}
	.ivu-table-wrapper-with-border{
		border 0!important;
	}
	/deep/.ivu-table-border th, /deep/.ivu-table-border td{
		border-right 0!important;
	}
</style>
