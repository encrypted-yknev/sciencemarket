$(document).ready(function()	{
	$(".col-section,.col-section-side").click(function()	{
		$("#main-body").hide();
		$(".head-nav2").hide();
		$("#auth-cards").hide();
		$("#auth-form").hide();
        $(".right-head .head-nav1").attr("onclick","showCollabView(1,1)");
		$(".right-head .head-nav1").html("<span class='glyphicon glyphicon-plus'></span>&nbsp;PROPOSE COLLABORATION");
		$(".head-nav1").show();
		$("#col-cards").show();
		$("#main-cont").show();
	});
	$(".auth-section,.auth-section-side").click(function()	{
		$("#main-body").hide();
		$(".head-nav1").hide();
		$("#col-cards").hide();
		$("#col-form").hide();
        $(".right-head .head-nav2").attr("onclick","showCollabView(2,1)");
		$(".right-head .head-nav2").html("<span class='glyphicon glyphicon-plus'></span>&nbsp;PROPOSE AUTHORSHIP");
		$(".head-nav2").show();
		$("#auth-cards").show();
		$("#main-cont").show();
	});
/*
	$(".head-nav1").click(function()	{
		$("#col-cards").hide();
        $(".right-head .head-nav1").html("JOB CARDS");
		$("#col-form").show();
	});
	$(".head-nav2").click(function()	{
		$("#auth-cards").hide();
        $(".right-head .head-nav2").html("JOB CARDS");
		$("#auth-form").show();
	});*/
});

function showCollabView(requestTyp,flag)   {
    if(requestTyp == 1) {    
        if(flag == 1)   {   
            $("#col-cards").hide();
            $(".right-head .head-nav1").attr("onclick","showCollabView(1,2)");
            $(".right-head .head-nav1").html("JOB CARDS");
		    $("#col-form").show();
        }
        else if(flag == 2)  {
            $("#col-form").hide();        
            $(".right-head .head-nav1").attr("onclick","showCollabView(1,1)");    
            $("#col-cards").show();
            $(".right-head .head-nav1").html("<span class='glyphicon glyphicon-plus'></span>&nbsp;PROPOSE COLLABORATION");		    
        }
    }
    else if(requestTyp == 2)    {
        if(flag == 1)   {   
            $("#auth-cards").hide();
            $(".right-head .head-nav2").attr("onclick","showCollabView(2,2)");
            $(".right-head .head-nav2").html("JOB CARDS");
		    $("#auth-form").show();
        }
        else if(flag == 2)  {
            $("#auth-form").hide();            
            $(".right-head .head-nav2").attr("onclick","showCollabView(2,1)");
            $("#auth-cards").show();
            $(".right-head .head-nav2").html("<span class='glyphicon glyphicon-plus'></span>&nbsp;PROPOSE AUTHORSHIP");		    
        }
    }
}
function postCollaboration(requestTyp)	{
	/*	Collaboration */
	var validFlag = true;
	var msg = "";
	if(requestTyp == 1)	{
		var title = document.getElementById("title").value;
		var summary = document.getElementById("summary").value;
		var skills = document.getElementById("skills").value;
		var users = document.getElementById("users").value;
		var univ = document.getElementById("univ").value;
		var loc = document.getElementById("loc").value;
		var strtDt = document.getElementById("dt1").value;
		var endDt = document.getElementById("dt2").value;
		var skillsReq = document.getElementById("skills2").value;
	}
	/*	Authorship */
	else if(requestTyp == 2)	{
		var title = document.getElementById("title1").value;
		var summary = document.getElementById("summary1").value;
		var stage = document.getElementById("stage").value;
		var users = document.getElementById("users1").value;
		var desc = document.getElementById("desc").value;
		var authid = document.getElementById("authid").value;
		var univ = document.getElementById("univ1").value;
		var loc = document.getElementById("loc1").value;
		var strtDt = document.getElementById("strtdt1").value;
		var endDt = document.getElementById("enddt2").value;
		var skillsReq = document.getElementById("skills21").value;
	}
	if(title=="" || summary=="" || users=="" || univ=="" || loc=="" || strtDt=="" || endDt=="" || skillsReq=="")	{
		msg="<div class='alert alert-danger'>All fields are required</div>";
		validFlag=false;
	}
	
	if(validFlag)	{
		if((requestTyp==1 && skills=="") || (requestTyp==2 && (stage=="" || desc=="" || authid=="")))	{
			msg="<div class='alert alert-danger'>All fields are required</div>";
			validFlag=false;
		}
	}
	
	if(validFlag)	{
		if(strtDt > endDt)	{
			msg="<div class='alert alert-danger'>Estimated start date cannot be beyond end date</div>";
			validFlag=false;
		}
		else	{
			sDate = new Date(strtDt);
			eDate = new Date(endDt);
			if(sDate < new Date() || eDate < new Date())	{
				msg = "<div class='alert alert-danger'>Start date or end date should be greater than today's date</div>"
				validFlag=false;
			}
		}
	}
	if(validFlag)	{
		//store details       
		$.ajax({
			type:"post",
			url:"post_collaboration.php",
			data:
			{
				"requestTyp":requestTyp,
				"title":title,
				"summary":summary,
				"users":users,
				"univ":univ,
				"loc":loc,
				"strtDt":strtDt,
				"endDt":endDt,
				"skillsReq":skillsReq,
				"skills":skills,
				"stage":stage,
				"desc":desc,
				"authid":authid
			},
			beforeSend:function()	{
				if(requestTyp==1)	{
					$("#message-col").html("<div class='alert alert-info'>Posting...</div>")
				}
				else if(requestTyp==2)	{
					$("#message-auth").html("<div class='alert alert-info'>Posting...</div>");
				}
			},
			success:function(res)	{
				if(res == "1")	{               
					msg="<div class='alert alert-success'>Posted!</div>";
				}
				else if(res == "2")	{
					msg="<div class='alert alert-danger'>Cannot post!</div>";
				}
				else 	{
					msg="<div class='alert alert-danger'>Some error occurred. Please try again later!"+res+"</div>";
				}		
                if(requestTyp==1)	{
		            $("#message-col").html(msg);
	            }
	            else if(requestTyp==2)	{
		            $("#message-auth").html(msg);	
	            }			
			}
		});
	}	
}
