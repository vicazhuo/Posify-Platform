module.exports = {
    /**
     * 封装好的接口
     * @param url
     * @param data
     * @param headers
     * @param method
     * @returns {Promise<boolean|HttpClientResponse<any>>}
     */
    async otaCurl(url = '', data = {}, headers = {}, method = 'POST') {
        const {ctx, app} = this;
        if (url == '') {
            return false
        }
        if (!headers.Cookie) {
            headers.Cookie = 'abd'
        }
        return await app.curl(url, {
            dataType: 'json',
            method: method,
            headers: headers,
            data: data
        });
    },
    /**
     *
     * @param date
     * @param fmt
     * @returns {Promise<string>}
     */
    async dateFormat(date,fmt='YYYY-mm-dd HH:MM') {

        let ret;
        const opt = {
            "Y+": date.getFullYear().toString(),        // 年
            "m+": (date.getMonth() + 1).toString(),     // 月
            "d+": date.getDate().toString(),            // 日
            "H+": date.getHours().toString(),           // 时
            "M+": date.getMinutes().toString(),         // 分
            "S+": date.getSeconds().toString()          // 秒
            // 有其他格式化字符需求可以继续添加，必须转化成字符串
        };
        for (let k in opt) {
            ret = new RegExp("(" + k + ")").exec(fmt);
            if (ret) {
                fmt = fmt.replace(ret[1], (ret[1].length == 1) ? (opt[k]) : (opt[k].padStart(ret[1].length, "0")))
            };
        };
        return fmt;
    }
};
