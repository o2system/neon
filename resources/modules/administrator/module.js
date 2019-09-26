$('.insert-role').on('click',function () {
    var role = $(this).data('role');
    var segment = $(this).data('segment');
    var permission = $(this).data('permission');
    $.ajax({
        url:espresso.helpers.url.base("api/system/modules/add-role"),
        type:"POST",
        data:{
            id_sys_user_segment: segment,
            id_sys_module_role : role,
            permission : permission
        },
        success:function() {
            console.log('success');
        },
        error:function(){
            console.log('danger');
        }

    });
});
$('.insert-user').on('click',function () {
    var role = $(this).data('user');
    var segment = $(this).data('segment');
    var permission = $(this).data('permission');
    $.ajax({
        url:espresso.helpers.url.base("api/system/modules/add-user"),
        type:"POST",
        data:{
            id_sys_module_segment: segment,
            id_sys_module_user : role,
            permission : permission
        },
        success:function() {
            console.log('success');
        },
        error:function(){
            console.log('danger');
        }

    });
});