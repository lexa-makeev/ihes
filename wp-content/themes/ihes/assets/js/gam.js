$(document).ready(function(){
    $(".gam_icon").click(function () {
        $(".gam").addClass("gam_active");
    });
    $(".krest").click(function () {
        $(".gam").removeClass("gam_active");
    });
});