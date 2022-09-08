module.exports = (app) => {
    const { router, controller } = app;
    router.get('/', controller.home.test);
    router.get('/zzopen/waresshow/baseInfo', controller.home.baseInfo);
    router.get('/zzopen/waresshow/moreInfo', controller.home.moreInfo);
    router.get('/zzopen/hypermall/sameModelEvaluateStatistics', controller.home.sameModelEvaluateStatistics);
    router.post('/api/getHoseList', controller.house.houseList);
    router.post('/api/getHoseDetails', controller.house.houseDetails);
};
