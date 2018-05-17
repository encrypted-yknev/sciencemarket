$(document).ready(function()    {
    $("#check-all").change(function()   {
        if(this.checked)    {
            $(".subgroup-sec").attr("disabled","disabled");
            $(".grp-names").css("color","#ddd");
        }
        else    {
            $(".subgroup-sec").removeAttr("disabled");
            $(".grp-names").css("color","#000");
        }
    });
});

function fetchGroupPosts(groupId)   {
    var subgroups = new Array();
    $(".subgroup-sec:checked").each(function() {
       subgroups.push($(this).val());
    });

    $.ajax({
        url:"fetch_group_posts.php",
        type:"post",
        data:
        {   
            "group_id":groupId,
            "sort":document.getElementById("sort-id").value,
            "subgroups":subgroups
        },
        beforeSend:function()   {
            $("#middle-container").html("Loading...");
        },
        success:function(res)   {
            $("#middle-container").html(res);
        }

    });
}



