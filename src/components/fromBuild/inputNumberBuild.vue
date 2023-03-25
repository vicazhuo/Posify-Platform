<template>
    <div>
        <FormItem :label="title" class="input-build" :class="getClassName()">
            <div class="input-error-wrapper">
                <InputNumber @on-blur="changeEvent('blur',$event)" @on-keyup="changeEvent('keyup',$event)" @on-focus="changeEvent('focus',$event)" @on-click="changeEvent('click',$event)" @on-change="changeEvent('change',$event)" v-model="valueModel" :name="field" :min="min === null ? -Infinity : min" :max="max === null ? Infinity : max" :placeholder="placeholder" :style="styleModel"/>
                <!--错误提醒-->
                <div v-if="errorMessage" class="error-wrapper">{{errorMessage}}</div>
            </div>
            <!--说明-->
            <div v-if="info" class="info-wrapper">{{info}}</div>
        </FormItem>
    </div>
</template>

<script>
    import build from "./build";
    export default {
        name: "inputNumberBuild",
        mixins: [build],
        props: {
            max: {
                type: Number,
                default: Infinity
            },
            min: {
                type: Number,
                default: -Infinity
            },
        },
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
