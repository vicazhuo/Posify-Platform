<template>
    <div class="form-submit">
        <!--创建表单-->
        <Form :ref="'formSubmit'+field" :model="submitValue" :label-width="124">
            <use-component @changeValue="changeValue" :errorsValidate="errorsValidate" :validate="validate" :rules="rules"></use-component>
            <!--自定义内容-->
            <slot name="content" class="content"></slot>
            <Card v-if="buttonHide" :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
                <FormItem>
                    <Button type="primary" :disabled="disabled" :loading="loading" @click="submit">{{butName}}</Button>
                </FormItem>
            </Card>
            <!--button-->
            <slot name="button" class="slot-button"></slot>

        </Form>

    </div>
</template>

<script>
    import Schema from 'async-validator';
    import {Message} from "iview";
    import request from '@/plugins/request';
    import { mapMutations } from "vuex";

    export default {
        name: "fromSubmit",
        components:{
            useComponent:() => import('../fromBuild/useComponent'),
        },
        props:{
            rules:{
                type:Array,
                default(){
                    return [];
                }
            },
            validate:{
                type:Object,
                default(){
                    return {};
                }
            },
            butName:{
                type:String,
                default:'提交',
            },
            field:{
                type:String,
                default(){
                    return ''+Math.random();
                },
            },
            url:{
                type:String,
                default:"",
            },
            method:{
                type:String,
                default:"POST",
            },
            buttonHide:{
                type: Boolean,
                default: true,
            },
            on:{
                type:Object,
                default(){
                    return {};
                },
            },
        },
        data() {
            return {
                submitValue: {},
                disabled: false,
                loading: false,
                errorsValidate: [],
            };
        },
        watch: {
            rules: {
                handler() {
                    this.submitValue = this.getRuleValue(this.rules);
                },
                deep: true,
            },
        },
        mounted() {
            this.submitValue = this.getRuleValue(this.rules);
            this.setCopyrightShow({ value: false });
        },
        destroyed () {
            this.setCopyrightShow({ value: true });
        },
        methods:{
            ...mapMutations('admin/layout', [
                'setCopyrightShow',
								"isMobile",
								"menuCollapse"
            ]),
            //组件值变动事件
            changeValue(e) {
                this.submitValue[e.field] = e.value;
                this.rules = this.setRuleValue(this.rules,e.field, e.value);
            },
            //设置组件值
            setRuleValue(rules, field, vvvv) {
                rules.map(item =>{
                    if (item.field !== undefined && item.field === field) {
                        item.value = vvvv;
                    }
                    if (typeof item.options === 'object') {
                        item.options.map(option => {
                            if (option.componentsModel !== undefined) {
                                option.componentsModel = this.setRuleValue(option.componentsModel, field, vvvv);
                            }
                        });
                    }
                    if (typeof item.control === 'object') {
                        item.control.map(value => {
                            if (value.componentsModel !== undefined) {
                                value.componentsModel = this.setRuleValue(value.componentsModel, field, vvvv);
                            }
                        });
                    }
                    if (typeof item.componentsModel === "object") {
                        item.componentsModel = this.setRuleValue(item.componentsModel, field, vvvv);
                    }
                });
                return rules;
            },
            //获取默认值
            getRuleValue(rules) {
                let submitValue = {};
                rules.map(item=>{
                    if (item.field !== undefined) {
                        submitValue[item.field] = item.value
                    }
                    if (typeof item.options === 'object') {
                        item.options.map(option => {
                            if (option.componentsModel !== undefined) {
                                let values = this.getRuleValue(option.componentsModel);
                                Object.assign(submitValue, values);
                            }
                        });
                    }
                    if (typeof item.control === 'object') {
                        item.control.map(value => {
                            if (value.componentsModel !== undefined) {
                                let values = this.getRuleValue(value.componentsModel);
                                Object.assign(submitValue, values);
                            }
                        });
                    }
                    if (typeof item.componentsModel === "object") {
                        let values = this.getRuleValue(item.componentsModel);
                        Object.assign(submitValue, values);
                    }
                })
                return submitValue;
            },
            //表单提交
            submit(){
                let  validator = new Schema(this.validate);
                validator.validate(this.submitValue,(error) => {
                    if (error === undefined || error === null) {
                        this.errorsValidate = [];
                        this.disabled = true;
                        this.loading = true;

                        if (this.on['save']) {
                            try {
                                this.on['save'](this.submitValue, ()=> this.disabled = false, ()=> this.loading = false)
                            } catch (e) {
                                Message.error(err || '提交失败')
                            }
                        } else {
                            request[this.method.toLowerCase()](this.url, this.submitValue).then((res) => {
                                Message.success(res.msg || '提交成功')
                                this.on['submit'] && this.on['submit'](res)
                            }).catch(err => {
                                Message.error(err.msg || '提交失败')
                            }).finally(() => {
                                this.disabled = false;
                                this.loading = false;
                            })
                        }
                    } else {
                        this.errorsValidate = error;
                        Message.error(error[0].message);
                    }
                });

            },
        }
    }
</script>

<style lang="less" scoped>
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
        z-index: 10;
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
