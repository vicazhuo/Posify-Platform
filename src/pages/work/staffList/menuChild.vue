<template>
    <Submenu :name="parentItem.department_id">
        <template slot="title">
             <Icon type="ios-folder" size="15" color="#FFCA28" />
            <span>{{ parentItem.name }}</span>
        </template>
        <template v-for="item in parentItem.children">
            <side-menu-item 
                v-if="item.children&&item.children.length!==0" 
                :parent-item="item" 
                :key="'menu-'+item.name"
            >
            </side-menu-item>
            <menu-item v-else :name="item.department_id" :key="'menu-'+item.name">
                 <Icon type="ios-folder" size="15" color="#FFCA28" />
                <span>{{ item.name }} ({{item.count}})</span>
            </menu-item>
        </template>
    </Submenu>
</template>

<script>
export default {
    name: 'sideMenuItem',
    props: {
        parentItem: {
            type: Object,
              default: () => {}
        }
    }
}
</script>

<style>

</style>