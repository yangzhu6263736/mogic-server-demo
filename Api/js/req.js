var req = {
    indexModules: function (params, next) {
        mui.ajax('http://60.191.205.121:3736/api/api/index', {
            data: {
                username: 'username',
                password: 'password'
            },
            dataType: 'json',//服务器返回json格式数据
            type: 'post',//HTTP请求类型
            timeout: 10000,//超时时间设置为10秒；
            headers: { 'Content-Type': 'application/json' },
            success: function (data) {
                next(false, data)
            },
            error: function (xhr, type, errorThrown) {
                //异常处理；
                console.log(type);
            }
        });
    }
}
