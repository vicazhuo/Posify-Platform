<template>
    <FormItem :label="title" class="input-build" :class="getClassName()" :prop="field">
        <div class="input-error-wrapper">
            <Select v-model="valueModel" :placeholder="placeholder" :multiple="multiple" :filterable="filterable" @on-clear="changeEvent('clear',$event)" @on-change="changeEvent('change',$event)">
                <Option v-for="item in options" :value="item.value" :key="item.value">{{ item.label }}</Option>
            </Select>
            <!-- 错误提醒 -->
            <div v-if="errorMessage" class="error-wrapper">{{errorMessage}}</div>
        </div>
        <!-- 说明 -->
        <div v-if="info" class="info-wrapper">{{ info }}</div>
    </FormItem>
</template>

<script>
    import build from "./build";
    export default {
        name: "selectBuild",
        mixins:[build],
        props:{
            multiple:{
                type:Boolean,
                default:false,
            },
            filterable:{
                type:Boolean,
                default:false,
            },
        },
        mounted() {

        }
    }
</script>

<style scoped>
    @import url('./css/build.css');
</style>
