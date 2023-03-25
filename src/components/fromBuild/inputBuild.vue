<template>
    <div>
        <FormItem :label="title" class="input-build" :class="getClassName()">
            <div class="input-error-wrapper">
                <Input :prefix="prefix" @on-blur="changeEvent('blur',$event)" @on-keyup="changeEvent('keyup',$event)" @on-focus="changeEvent('focus',$event)" @on-click="changeEvent('click',$event)" @on-change="changeEvent('change',$event)" v-model="valueModel" :name="field" :placeholder="placeholder" :maxlength="maxlength !== 0 ? maxlength : null" :rows="rows" :disabled="disabled" :type="type ? type :'text'" :style="styleModel" :suffix="suffix"/>
                <!--复制-->
                <Button v-if="copy" v-clipboard="valueModel" v-clipboard:success="handleCopySuccess" type="primary">{{copyText}}</Button>
                <!--随机token-->
                <Button v-if="randToken !== 0"  @click="randTokenClick" type="primary">随机生成</Button>
                <!--错误提醒-->
                <div v-if="errorMessage && !copy" class="error-wrapper">{{errorMessage}}</div>
            </div>
            <!--说明-->
            <div v-if="info" class="info-wrapper">{{info}}</div>
        </FormItem>
    </div>
</template>

<script>
    import build from "./build";
    export default {
        name: "inputBuild",
        mixins:[build],
        props:{
            rows: {
                type: Number,
                default: 2
            },
            disabled: {
                type: Boolean,
                default: false
            },
            copyText: {
                type: String,
                default: false
            },
            copy: {
                type: Boolean,
                default: false
            },
            randToken: {
                type: Number,
                default: 0,
            },
            maxlength: {
                type: Number,
                default: 0,
            }
        },
        methods: {
            handleCopySuccess(){
                this.$Message.success('复制成功');
            },
            encodingAESKeyGen() {
                 let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                 let token = '';
                 for(var i = 0; i < 43; i++) {
                    var j = parseInt(Math.random() * 61 + 1);
                    token += letters[j];
                 }
                 return token;
            },
            token() {
                let letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                let token = '';
                for(var i = 0; i < 32; i++) {
                    var j = parseInt(Math.random() * 61 + 1);
                    token += letters[j];
                }
                return token;
            },
            randTokenClick() {
                if (this.valueModel) {
                    this.$Modal.confirm({
                        title: this.title +'值已存在是否重新生成?',
                        onOk: () => {
                            this.changeToken();
                        }
                    });
                } else {
                    this.changeToken();
                }
            },
            changeToken(){
                if (this.randToken === 1) {
                    this.valueModel = this.token()
                    this.$emit('changeValue',{field: this.field, value: this.valueModel});
                } else if (this.randToken === 2) {
                    this.valueModel = this.encodingAESKeyGen()
                    this.$emit('changeValue',{field: this.field, value: this.valueModel});
                }
            }
        },
    }
</script>

<style scoped>
    @import url('./css/build.css');
    .ivu-btn-primary {
        margin-left: 10px;
    }
</style>
