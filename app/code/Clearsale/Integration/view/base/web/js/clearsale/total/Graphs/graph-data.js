var chart;
var table;
var dataChart;
var dataTable;
var jsonTableData;
var requestDashboard;
var ApprovalChart;
var DeclineChart;
var SLAChart;
var SubmissionChart;
var ApprovalData;
var DeclineData;
var SLAData;
var SubmissionData;
var ApprovalRate;
var SubmissionCount = 0;
var AnalysisCount = 0;
var LastOrders;
var dataChart = { Data: [] };

var lastOrderApproved = 0;
var lastOrderAnalysing = 0;
var lastOrderDeclined = 0;
var lastOrderCanceled = 0;
var isUpdate = false;
var sent = false;


jQuery(document).ready(function () {
	jQuery.support.cors = true;
	SetRequestDashboard();
	BindChart();


	jQuery("#ReportFilter").change(function () {
		isUpdate = true;
		interval = jQuery(this).val();        
		SetRequestDashboard();
		BindChart();		
	});

});

function logArrayElements(element, index, array) {

	var item = array[index];

	dataChart.Data.push({
		"label": item.Description,
		"value": item.Quantity
	});
}

function RequestLogin() {

	var requestLogin = {
	Login: {
	Apikey: apiKey,
	ClientID: user,
	ClientSecret: password
		}
	};

	requestLogin = JSON.stringify(requestLogin);

	jQuery.ajax({
	url: prefix + "api/auth/Login",
	type: "POST",
	contentType: "application/json",
	data: requestLogin,
	async: false,
	success: function (result) {
			token = result.Token.Value;
		}, error: function (xhr, textStatus, errorThrown) {
			console.log(textStatus + ':' + errorThrown + ' | ' + xhr.responseText);
		}
	});
}

function SetRequestDashboard()
{
	requestDashboard = {
LoginToken: loginToken,
ApiKey: apiKey,
Interval: interval,
Status: "3"
	};

	requestDashboard = JSON.stringify(requestDashboard);
	
	console.log(requestDashboard);
}

function BindSubmissionCount() {
	jQuery.support.cors = true;

	var SubmissionCount = "";
	
	jQuery.ajax(
	{
url: prefix + "Api/Report/CountSubmission",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(SubmissionCount) {
		jQuery("#Submited").html(SubmissionCount);
	});		     

}

function BindApprovalRate() {
	jQuery.support.cors = true;

	var jsonData = "";
	
	jQuery.ajax(
	{
url: prefix + "Api/Report/ApprovalRate",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(ApprovalRate){
		jQuery("#ApprovalRate").html(ApprovalRate +"%");
	});		     
}

function BindAnalysisCount() {
	jQuery.support.cors = true;
	requestDashboard.Status = 3;

	var AnalysisCount = "";
	
	jQuery.ajax(
	{
url: prefix + "Api/Report/CountByStatus",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(AnalysisCount){
		jQuery("#InAnlaysis").html(AnalysisCount);
	});		     

}

function BindLastOrders() {
	jQuery.support.cors = true;
	requestDashboard.Status = 3;

	var LastOrders = "";
	
	jQuery.ajax(
	{
url: prefix + "Api/Report/LastOrders",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(LastOrders){
		MountTableLastOrders(LastOrders);
	});		    ;

}

function BindSubmission() {
	jQuery.support.cors = true;
	var SubmissionData = "";
	console.log(requestDashboard);
	
	jQuery.ajax(
	{
url: prefix + "Api/Report/Approvals",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(SubmissionData) {

		//if(!isUpdate)
		//{
		//	SubmissionChart = new Morris.Area({
		//	element: 'curved-line-chart',
		//	data: SubmissionData,
		//	xkey: 'Data',
		//	ykeys: ['Aprovados', 'Quantidade'],
		//	labels: ['Approved', 'Integration'],
		//	lineColors: ['#7C1952', '#FFA500'],
		//	pointSize: 2,
		//	hideHover: 'auto',
		//	resize: true
		//	});
		//}else
		//{
		//	SubmissionChart.setData(SubmissionData);
		//}


	});		     
}

function BindReproved() {
	jQuery.support.cors = true;
	var jsonData = "";

	jQuery.ajax(
	{
url: prefix + "Api/Report/Reproved",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(jsonData) {
		reprovedData = jsonData;
		dataChart = { Data: [] }
		reprovedData.forEach(logArrayElements)
		DeclineData = dataChart.Data;
		//if(!isUpdate || DeclineChart.data == null)
		//{
		//	DeclineChart = new Morris.Donut({
		//	element: 'decline-chart',
		//	data: DeclineData,
		//	colors: ['#660000', '#990000', '#CC0000', '#FF0000'],
		//	resize: true
		//	});
		//}else
		//{
		//	DeclineChart.setData(DeclineData);
		//}
	});


}

function BindSLA() {
	jQuery.support.cors = true;
	var SLAData = "";

	jQuery.ajax(
	{
url: prefix + "Api/Report/SLA",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(SLAData) {
		
		//if(!isUpdate)
		//{
		//	SLAChart = new Morris.Bar({
		//	element: 'description-chart',
		//	data: SLAData,
		//	xkey: 'Label',
		//	ykeys: ['Value'],
		//	labels: ['Integration'],
		//	barColors: ['#FAA519'],
		//	barRatio: 0.4,
		//	xLabelAngle: 35,
		//	hideHover: 'auto',
		//	resize: true
		//	});
		//}else
		//{
		//	SLAChart.setData(SLAData);
		//}
	});

}

function BindApproved() {

	jQuery.support.cors = true;
	var orderStatusSummary = "";

	var orderStatusSummaryData = jQuery.ajax(
	{
url: prefix + "Api/Report/OrderStatusSummary",
type: "POST",
contentType: "application/json",
data: requestDashboard,
async: true
	}
	).done(function(orderStatusSummary) {
		dataChart =  { Data: [] }
		orderStatusSummary.forEach(logArrayElements);
		ApprovalData = dataChart.Data;
		
		//if(!isUpdate || ApprovalChart.data == null)
		//{
		//	ApprovalChart = Morris.Donut({
		//	element: 'approval-chart',
		//	data: ApprovalData,
		//	colors: ['#009900', '#990000'],
		//	resize: true
		//	});
		//}else
		//{
		//	ApprovalChart.setData(ApprovalData);
		//}
		
	});


}

function BindChart() {

	BindSubmission();
	BindReproved();
	BindSLA();
	BindApproved();
	BindSubmissionCount();
	BindApprovalRate();
	BindAnalysisCount();
	BindLastOrders();
}



function getFormattedDate(thedate) {
    var date = new Date(thedate);

    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hour = date.getHours();
    var min = date.getMinutes();
    var sec = date.getSeconds();

    month = (month < 10 ? "0" : "") + month;
    day = (day < 10 ? "0" : "") + day;
    hour = (hour < 10 ? "0" : "") + hour;
    min = (min < 10 ? "0" : "") + min;
    sec = (sec < 10 ? "0" : "") + sec;

    var str = month + "/" + day + "/" + date.getFullYear() + ' ' +  hour + ":" + min + ":" + sec;

    /*alert(str);*/

    return str;
}


function MountTableLastOrders(LastOrders) {
	var html = "";

	html+="<table class=\"table table-striped bootgrid-table\">";
	html+="<thead>";
	html+="<tr>";
	html+="<th>Date</th>";
	html+="<th>Order</th>";
	html+="<th>Value</th>";
	html+="<th>Status</th>";
	html+="</tr>";
	html+="</thead>";
	html += "<tbody>";

	lastOrderApproved = 0;
	lastOrderAnalysing = 0;
	lastOrderDeclined = 0;
	lastOrderCanceled = 0;

	jQuery.each(LastOrders, function (i, data) {

		html += "<tr>";
		html += "<td>"+getFormattedDate(data.OrderActionData)+"</td>";
		html += "<td>" + "<a target=\"_blank\" href=\""+ environmentRoot +"/Order/DetailByOrderId/"
		+ data.ClientOrderId + "\" target=\"_blank\">" + data.ClientOrderId + "</a></td>";
		html += "<td>" + "$ " + data.TotalOrderValue.toFixed(2) + "</td>";
		html += setStatus(data.Status);
		html += "</tr>";
	});

	html+="</tbody>";
	html+="</table>";

	var last = JSON.stringify(LastOrders);

	jQuery("#lastorders").html(html);
	jQuery("#lastOrderApproved").html(lastOrderApproved);
	jQuery("#lastOrderAnalysing").html(lastOrderAnalysing);
	jQuery("#lastOrderCanceled").html(lastOrderCanceled);
	jQuery("#lastOrderDeclined").html(lastOrderDeclined);
}


function setStatus(sStatus)
{

	var html = "";
	switch (sStatus)
	{
	case "Approved":
	case "Automatic Approved": html += "<td class=\"c-green\"><span class=\"md-done-all\"></span> Approved</td>"; lastOrderApproved++; break;
	case "Analysing": html += "<td class=\"c-cyan\"><span class=\"md md-multitrack-audio\"></span> Analysing</td>"; lastOrderAnalysing++; break;
	case "Cancelled": html += "<td class=\"c-red\"><span class=\"md md-dnd-forwardslash\"></span> Cancelled</td>"; lastOrderCanceled++; break;
	case "Reproved": html += "<td class=\"c-brown\"><span class=\"md md-attach-money\"></span> Declined</td>"; lastOrderDeclined++; break;
	default: html += "<td class=\"c-brown\"><span class=\"md\"></span> " + sStatus + "</td>";
	}

	return html;
}

function sendPostPendingOrders() {
	if(!sent) {
		jQuery.ajax({
		    type: "POST",
		    //url: "admin/integration/sendPendingOrdersClearSale", 
		    data: jQuery('#clearForm').serialize(),
		    success: function() {   
		        window.location = window.location.href.split("?")[0];
		        window.location.reload();
		    }
		});
	}
	sent = true;
    return false;
}


function sendPostUpdateOrders() {
	if(!sent) {
		jQuery.ajax({
		    type: "POST",
		    //url: "admin/integration/sendPendingOrdersClearSale", 
		    data: jQuery('#clearUpdate').serialize(),
		    success: function() {   
		        window.location = window.location.href.split("?")[0];
		        window.location.reload();
		    }
		});
	}
	sent = true;
    return false;
}


