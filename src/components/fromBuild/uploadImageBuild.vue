<template>
    <FormItem :label="title" class="input-build upload-build" :class="getClassName()">
        <!-- 多文件-->
        <template v-if="multiple">
            <template v-if="type === 'file'">
                <div class="demo-upload-list" v-for="(item,index) in uploadList">
                    <Icon type="md-document" size="40" />
                    <div class="demo-upload-list-cover">
                        <Icon type="ios-trash-outline" @click.native="handleRemove(index)"></Icon>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="demo-upload-list" v-for="(item,index) in uploadList">
                    <img :src="item">
                    <div class="demo-upload-list-cover">
                        <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                        <Icon type="ios-trash-outline" @click.native="handleRemove(index)"></Icon>
                    </div>
                </div>
            </template>
        </template>
        <!-- 单个文件-->
        <template v-else>
            <div class="demo-upload-list" v-if="valueModel">
                <template v-if="type === 'file'">
                    <Icon type="md-document" size="40" />
                    <div class="demo-upload-list-cover">
                        <Icon type="ios-trash-outline" @click.native="handleRemove(valueModel)"></Icon>
                    </div>
                </template>
                <template v-else>
                    <img :src="valueModel">
                    <div class="demo-upload-list-cover">
                        <Icon type="ios-eye-outline" @click.native="handleView(valueModel)"></Icon>
                        <Icon type="ios-trash-outline" @click.native="handleRemove(valueModel)"></Icon>
                    </div>
                </template>
            </div>
        </template>
        <!-- 上传组件-->
        <div class="input-error-wrapper">
            <Upload
                    ref="upload"
                    v-if="disabledSeleteImage"
                    :show-upload-list="false"
                    :on-success="handleSuccess"
                    :format="upload.format"
                    :max-size="upload.size"
                    :on-progress="handProgress"
                    :on-format-error="handleFormatError"
                    :on-exceeded-size="handleMaxSize"
                    :before-upload="handleBeforeUpload"
                    :multiple="multiple"
                    type="drag"
                    :accept = 'type === "file" ? "":"image/*"'
                    :action="upload.url"
                    :headers="upload.headers"
                    style="display: inline-block;width:58px;">
                <div style="width: 58px;height:58px;line-height: 58px;">
                    <Icon :type="icon ? icon:'ios-image'" size="20"></Icon>
                </div>
            </Upload>
            <!--错误提醒-->
            <div v-if="errorMessage" class="error-wrapper">{{ errorMessage }}</div>
        </div>
        <!--说明-->
        <div v-if="info" class="info-wrapper">{{ info }}</div>

        <look-image ref="lookImage" :imageUrl="imageUrl"></look-image>
    </FormItem>
</template>

<script>
    import build from "./build";
    import Setting from '@/setting';
    import lookImage from "./lookImage";

    export default {
        name: "uploadImageBuild",
        mixins:[build],
        components:{lookImage},
        props: {
            //上传配置
            upload:{
                type: Object,
                default() {
                    return {
                        url:'',
                        size: 2048,
                        format: ['jpg','jpeg','png','gif'],
                        headers: {},
                        maxNum: 1
                    };
                }
            },
            icon: {
                type: String,
                default:''
            },
        },
        data(){
            return {
                uploadList: [],
                valueModel: this.value,
                disabledSeleteImage: false,
                errorMessage:'',
                imageUrl:'',
                multiple:false,
            };
        },
        watch:{
            valueModel:{
                handler(n){
                    if (this.multiple){
                        if (n.length >= this.upload.maxNum) {
                            this.disabledSeleteImage = false;
                        } else {
                            this.disabledSeleteImage = true;
                        }
                    } else {
                        this.disabledSeleteImage = !!!n;
                    }
                    this.changeValue();
                },
                deep:true
            },
        },
        created() {
            this.multiple = this.upload.maxNum > 1;
            if (this.multiple) {
                if (typeof this.valueModel !== 'object') {
                    this.valueModel = [];
                }
            } else {
                if (typeof this.valueModel !== 'string') {
                    this.valueModel = '';
                }
            }
            if (this.upload.url.indexOf('http') === -1 || this.upload.url.indexOf('https') === -1) {
                this.upload.url = Setting.apiBaseURL + this.upload.url;
            }

            if (['image','file'].indexOf(this.type) === -1) {
                this.type = 'image';
            }
            if (typeof this.valueModel === 'object' && !this.valueModel.length) {
                this.disabledSeleteImage = true;
            }
            if (typeof this.valueModel === 'string' && !this.valueModel) {
                this.disabledSeleteImage = true;
            }
        },
        mounted() {

        },
        methods:{
            changeValue(){
                this.$emit('changeValue',{field:this.field,value:this.valueModel});
                //触发change事件
                this.on['change'] && this.on['change'](this.valueModel);
            },
            handleSuccess (response, file, fileList) {
                if (this.on['success']) {
                    this.on['success'](response,(res)=> this.valueModel = res)
                    return;
                }
                if (response.status !== 200) {
                    this.$Message.error(response.msg);
                } else {
                    this.valueModel = response.data.src;
                    this.$Message.success(response.msg);
                }
            },
            handleFormatError () {

            },
            //上传进度
            handProgress(event, file, fileList) {

            },
            handleMaxSize() {

            },
            handleBeforeUpload() {

            },
            handleView(url) {
                this.imageUrl = url;
                this.$refs.lookImage.open();
            },
            handleRemove(item, index) {
                if (!this.multiple){
                    this.valueModel = '';
                } else {
                    this.valueModel.splice(index,1)
                }
            },
        }
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
