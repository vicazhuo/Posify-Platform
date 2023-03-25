<template>
  <div>
    <div class="mt20 ml20">
      <Form>
        <FormItem label="视频类型：">
		      <RadioGroup v-model="seletVideo" @on-change="changeVideo">
		        <Radio :label="0" class="radio">本地视频</Radio>
		        <Radio :label="1">视频链接</Radio>
		      </RadioGroup>
		    </FormItem>
        <FormItem label="视频链接"  v-if="seletVideo == 1">
          <Input style="width:250px;margin-right:14px;" v-model="videoLink" placeholder="请输入视频链接" />
          <Button type="primary" @click="zh_uploadFile">确认添加</Button>
        </FormItem>
        <FormItem label="本地上传：" v-if="seletVideo == 0">
          <input
        type="file"
        ref="refid"
        style="display: none"
        @change="zh_uploadFile_change"
      />
      <Button
        v-if="upload_type !== '1' || videoLink"
        type="primary"
        icon="ios-cloud-upload-outline"
        class="ml10"
        @click="zh_uploadFile"
        >上传视频</Button
      >
      <Upload
        v-if="upload_type === '1' && !videoLink"
        :show-upload-list="false"
        :action="fileUrl2"
        class="ml10"
        :before-upload="beforeUpload"
        :data="uploadData"
        :headers="header"
        :multiple="true"
        :on-success="handleSuccess"
        style="display: inline-block"
      >
        <Button type="primary" icon="ios-cloud-upload-outline">上传视频</Button>
      </Upload>
      <Progress :percent="progress" :stroke-width="5" v-if="upload.videoIng" />
        </FormItem>
      </Form>
      <div class="iview-video-style" v-if="formValidate.video_link">
        <video
          style="width: 100%; height: 100% !important; border-radius: 10px"
          :src="formValidate.video_link"
          controls="controls"
        >
          您的浏览器不支持 video 标签。
        </video>
        <div class="mark"></div>
        <Icon type="ios-trash-outline" class="iconv" @click="delVideo" />
      </div>
    </div>
    <div class="mt50 ml20">
      <Button type="primary" @click="uploads">确认</Button>
    </div>
  </div>
</template>

<script>
import { productGetTempKeysApi, uploadType } from "@/api/product";
import { uploadByPieces } from "@/utils/upload"; //引入uploadByPieces方法
import Setting from "@/setting";
import util from "@/libs/util";
// import "../../../public/UEditor/dialogs/internal";
export default {
  name: "vide11o",
  data() {
    return {
      fileUrl: Setting.apiBaseURL + "/file/upload",
      fileUrl2: Setting.apiBaseURL + "/file/video_upload",
      upload: {
        videoIng: false, // 是否显示进度条；
      },
      progress: 0, // 进度条默认0
      videoLink: "",
      formValidate: {
        video_link: "",
      },
      upload_type: "",
      uploadData: {},
      header: {},
      seletVideo:0
    };
  },
  created() {
    this.uploadType();
    this.getToken();
  },
  methods: {
    // 删除视频；
    delVideo() {
      let that = this;
      that.$set(that.formValidate, "video_link", "");
    },
    // 上传成功
    handleSuccess(res, file, fileList) {
      if (res.status === 200) {
        this.formValidate.video_link = res.data.src;
        this.$Message.success(res.msg);
      } else {
        this.$Message.error(res.msg);
      }
    },
	getToken() {
	  this.header["Authori-zation"] = "Bearer " + util.cookies.get("token");
	},
    // beforeUpload() {
    //   this.uploadData = {};
    //   let promise = new Promise((resolve) => {
    //     this.$nextTick(function () {
    //       resolve(true);
    //     });
    //   });
    //   return promise;
    // },
    	//获取视频上传类型
    uploadType() {
      uploadType().then((res) => {
        this.upload_type = res.data.upload_type;
      });
    },
    beforeUpload(file) {
		console.log(file,'123456');
		let imgTypeArr = ["video/mp4"];
		let imgType = imgTypeArr.indexOf(file.type) !== -1
		if (!imgType) {
			return this.$Message.warning({
			content:  '文件  ' + file.name + '  格式不正确, 请选择格式正确的视频',
			duration: 5
			});
		}
	  uploadByPieces({
	    randoms: "", // 随机数，这里作为给后端处理分片的标识 根据项目看情况 是否要加
	    file: file, // 视频实体
	    pieceSize: 3, // 分片大小
	    success: (data) => {
	      this.formValidate.video_link = data.file_path;
	      this.progress = 100;
	    },
	    error: (e) => {
	      this.$Message.error(e.msg);
	    },
	    uploading: (chunk, allChunk) => {
	      this.videoIng = true;
	      let st = Math.floor((chunk / allChunk) * 100);
	      this.progress = st;
	    },
	  });
	  return false;
	},
    zh_uploadFile() {
      if (this.videoLink && this.getFileType(this.videoLink) == 'video') {
        this.formValidate.video_link = this.videoLink;
      } else if(this.videoLink && this.getFileType(this.videoLink) !== 'video' && this.seletVideo == 1){
        return this.$Message.error("请输入正确的视频链接")
      } else {
        this.$refs.refid.click();
      }
    },
    zh_uploadFile_change(evfile) {
      let that = this;
      // console.log(evfile.target.files[0].type);
      // let suffix = evfile.target.files[0].name.substr(
      //   evfile.target.files[0].name.indexOf(".")
      // );
      // console.log(suffix);
      console.log(evfile.target.files[0].type);
      
      if (evfile.target.files[0].type !== "video/mp4") {
        return that.$Message.error("只能上传mp4文件");
      }
    //   productGetTempKeysApi().then((res) => {
    //     that.$videoCloud
    //       .videoUpload({
    //         type: res.data.type,
    //         evfile: evfile,
    //         res: res,
    //         uploading(status, progress) {
    //           that.upload.videoIng = status;
    //         },
    //       })
    //       .then((res) => {
    //         that.formValidate.video_link = res.url;
    //         that.$Message.success("视频上传成功");
    //       })
    //       .catch((res) => {
    //         that.$Message.error(res);
    //       });
    //   });
    // },
    uploadByPieces({
	    randoms: "", // 随机数，这里作为给后端处理分片的标识 根据项目看情况 是否要加
	    file: file, // 视频实体
	    pieceSize: 3, // 分片大小
	    success: (data) => {
	      this.formValidate.video_link = data.file_path;
	      this.progress = 100;
	    },
	    error: (e) => {
	      this.$Message.error(e.msg);
	    },
	    uploading: (chunk, allChunk) => {
	      this.videoIng = true;
	      let st = Math.floor((chunk / allChunk) * 100);
	      this.progress = st;
	    },
	  });
	  return false;
  },
    uploads() {
      if(this.videoLink) {
        this.$emit('getVideo',this.formValidate.video_link)
      } else {
        this.$Message.error("请上传视频");
      }
      
      
      // nowEditor.editor.execCommand(
      //   "inserthtml",
      //   `<p><img/><video src='${this.formValidate.video_link}' controls></video><br/></p>`
      // );
      // nowEditor.dialog.close(true);
    },
    getFileType(fileName) {
        // 后缀获取
        let suffix = '';
        // 获取类型结果
        let result = '';
        try {
        const flieArr = fileName.split('.');
        suffix = flieArr[flieArr.length - 1];
        } catch (err) {
        suffix = '';
        }
        // fileName无后缀返回 false
        if (!suffix) { return false; }
        suffix = suffix.toLocaleLowerCase();
        // 图片格式
        const imglist = ['png', 'jpg', 'jpeg', 'bmp', 'gif'];
        // 进行图片匹配
        result = imglist.find(item => item === suffix);
        if (result) {
        return 'image';
        }
        // 匹配 视频
        const videolist = ['mp4', 'm2v', 'mkv', 'rmvb', 'wmv', 'avi', 'flv', 'mov', 'm4v'];
        result = videolist.find(item => item === suffix);
        if (result) {
        return 'video';
        }
        // 其他 文件类型
        return 'other';
      },
    changeVideo(e){
      this.videoLink = "";
    }
  },
};
</script>

<style scoped>
.iview-video-style {
  width: 40%;
  height: 180px;
  border-radius: 10px;
  background-color: #707070;
  margin-top: 10px;
  position: relative;
  overflow: hidden;
}
.iview-video-style .iconv {
  color: #fff;
  line-height: 180px;
  width: 50px;
  height: 50px;
  display: inherit;
  font-size: 26px;
  position: absolute;
  top: -74px;
  left: 50%;
  margin-left: -25px;
}
.iview-video-style .mark {
  position: absolute;
  width: 100%;
  height: 30px;
  top: 0;
  background-color: rgba(0, 0, 0, 0.5);
  text-align: center;
}
</style>
