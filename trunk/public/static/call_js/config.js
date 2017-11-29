console.log(data);
var initOptions = {
    debug: true,
    strid: data.strid,
    uin: data.uin,
    admin_uin: data.admin_uin,
    appid: data.appid,//'4b08a3d3-8bde-4d96-b467-c41bcd08c552',
    access_token: data.access_token,
//  access_token: 'AC9BAFF0AFC1D941A7F97D2040066368B95D01A387FA6B67F8B9F2370AE7AE813B5B5033EBE8A6EF4B516C9CCD1EDB5E',
    server_url: 'http://vip.agent.tq.cn',
    keepalive: 30
};
var tq_hangup_id;
