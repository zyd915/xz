$(".show-order-info").click(function () {
    var oid = $(this).attr('value');
    $.layer({
        title: '订单查看',
        type: 1,
        offset: ['0px', ''],
        // maxmin: true,
        area: ['800px', 'auto'],
        page: {
            url: JSV.PATH_APP_SERVER + 'Order/orderInfo/oid/' + oid,
        }
    });
});


