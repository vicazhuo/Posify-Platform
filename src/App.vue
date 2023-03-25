<template>
  <div id="app">
    <keep-alive>
       <router-view v-if="isRouterAlive && $route.meta.keepAlive" />
    </keep-alive>
    <router-view v-if="isRouterAlive && !$route.meta.keepAlive" />
  </div>
</template>
<script>
import { on, off } from "view-design/src/utils/dom";
import { setMatchMedia } from "view-design/src/utils/assist";
import { mapMutations } from "vuex";
setMatchMedia();

export default {
  name: "app",
  provide (){
      return {
          reload:this.reload
      }
  },
  data(){
    return {
      isRouterAlive: true
    }
  },
  methods: {
    ...mapMutations("admin/layout", ["setDevice"]),
    handleWindowResize() {
      this.handleMatchMedia();
    },
    handleMatchMedia() {
      const matchMedia = window.matchMedia;

      if (matchMedia("(max-width: 600px)").matches) {
        var deviceWidth =
          document.documentElement.clientWidth || window.innerWidth;
        let css = "calc(100vw/7.5)";
        document.documentElement.style.fontSize = css;
        this.setDevice("Mobile");
      } else if (matchMedia("(max-width: 992px)").matches) {
        document.documentElement.style.fontSize = "12px";
        this.setDevice("Tablet");
      } else {
        document.documentElement.style.fontSize = "12px";
        this.setDevice("Desktop");
      }
    },
    reload(){
      this.isRouterAlive = false
      this.$nextTick(function () {
        this.isRouterAlive = true
      })
    }
  },
  mounted() {
    on(window, "resize", this.handleWindowResize);
    this.handleMatchMedia();
  },
  beforeDestroy() {
    off(window, "resize", this.handleWindowResize);
  },
};
</script>

<style lang="less">
.size {
  width: 100%;
  height: 100%;
}
html,
body {
  .size;
  //   overflow: hidden;
  margin: 0;
  padding: 0;
}
#app {
  .size;
}
</style>
