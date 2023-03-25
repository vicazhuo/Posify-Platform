<template>
    <div>
        <Alert :closable="closable" :show-icon="showIcon" :type="type ? type : 'info'">{{title}}</Alert>
    </div>
</template>

<script>
    import build from "./build";

    export default {
        name: "alertBuild",
        mixins: [build],
        props:{
            closable: {
                type: Boolean,
                default: false,
            },
            showIcon: {
                type: Boolean,
                default: false,
            }
        },
    }
</script>

<style scoped>
.ivu-alert {
    font-size: 12px;
    line-height: 12px;
}

.ivu-alert-warning {
    border-color: #FFF6E9;
    background-color: #FFF6E9;
    color: #FF9400;
}

.ivu-alert-warning >>> .ivu-alert-icon {
    font-size: 12px;
    color: #FF9400;
}
</style>
