import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import Sortable from "sortablejs";
import * as moment from "moment";
import JSGantt from "jsgantt-improved";
import "./assets/js/stopwatch";
import "./module.scss";

$(".checklist-tasks").on("click", function () {
	$.ajax({
		url: espresso.helpers.url.base("personal/boards/cards/tasks/change-check?id="+$(this).val()),
		success: function(){

		}
	});
});
$(".change-scrum-board-card-task-pipeline").on("click", function() {
	if($(this).val == 'BACKLOG' || $(this).val() == 'ON_PROGRESS'){
		$.ajax({
			url: espresso.helpers.url.base("personal/boards/cards/tasks/change-status"),
			type: "POST",
			typeData: "JSON",
			data: {status:$(this).val(), id_scrum_board_card_task:$("#id_scrum_board_task_card").val()},
			success: function(){
				Swal.fire({
					title: 'Success',
					text: 'Success Update',
					type: 'success'
				});
			}
		});
	}
});

$(".change-status-card-pipeline").on("click", function () {
	$.ajax({
		url: espresso.helpers.url.base("personal/boards/cards/change-status"),
		type: "POST",
		typeData: "JSON",
		data: {status:$(this).val(), id_scrum_board_card:$("#id_scrum_board_card").val()},
		success: function(){

		}
	});
});



document.addEventListener("DOMContentLoaded", function() {
	const calendarEl = document.getElementById("calendar");
	if(calendarEl instanceof Element) {
		$.ajax({
			url: espresso.helpers.url.base("api/personal/agenda"),
			success: function(data){
				agendaSuccess(data);
			}
		});

		function agendaSuccess(data){
			const calendar = new Calendar(calendarEl, {
				plugins: [ interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin ],
				header: {
					left: "prev,next today",
					center: "title",
					right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
				},
				defaultDate: moment().toDate(),
				navLinks: true, // can click day/week names to navigate views
				editable: true,
				eventLimit: true, // allow "more" link when too many events
				events: data.result,
			});
        
			calendar.render();
		}

	}

	// const sortableEl = document.querySelectorAll("[data-toggle=\"sortable\"]");
	// if(sortableEl.length > 0) {
	// 	sortableEl.forEach(function(el){
	// 		el.dataset.animation = el.dataset.animation || 150, el.dataset.filter = el.dataset.filter || ".ignore-sort", Sortable.create(el, el.dataset);
	// 	});
	// }

	const ganttEl = document.getElementById("GanttChart");

	if(ganttEl instanceof Element) {
		$.ajax({
			url: espresso.helpers.url.base("api/personal/boards/timeline?id_board=1"),
			success: function(data){
				ganttSuccess(data,ganttEl);
			}
		});
	}
	function ganttSuccess(data, ganttEl){
		const ganttChartElement = new JSGantt.GanttChart(ganttEl, "day");
	
		ganttChartElement.setOptions({
			vCaptionType: "Complete",  // Set to Show Caption : None,Caption,Resource,Duration,Complete,
			vQuarterColWidth: 36,
			vDateTaskDisplayFormat: "day dd month yyyy", // Shown in tool tip box
			vDayMajorDateDisplayFormat: "mon yyyy - Week ww",// Set format to display dates in the "Major" header of the "Day" view
			vWeekMinorDateDisplayFormat: "dd mon", // Set format to display dates in the "Minor" header of the "Week" view
			vLang: "en",
			vAdditionalHeaders: { // Add data columns to your table
				category: {
					title: "Category"
				},
				sector: {
					title: "Sector"
				}
			},
			vShowTaskInfoLink: 1, // Show link in tool tip (0/1)
			vShowEndWeekDate: 0,  // Show/Hide the date for the last day of the week in header for daily view (1/0)
			vUseSingleCell: 10000, // Set the threshold at which we will only use one cell per table row (0 disables).  Helps with rendering performance for large charts.
			vFormatArr: ["Day", "Week", "Month", "Quarter"], // Even with setUseSingleCell using Hour format on such a large chart can cause issues in some browsers
			vEvents: {
				taskname: console.log,
				res: console.log,
				dur: console.log,
				comp: console.log,
				startdate: console.log,
				enddate: console.log,
				planstartdate: console.log,
				planenddate: console.log,
				cost: console.log
			},
			vEventClickRow: console.log
		});
	
		// Load from a Json url
		JSGantt.parseJSON(data.result[0], ganttChartElement);
	
		// Or Adding  Manually
		// ganttChartElement.AddTaskItemObject(
		//     {
		//         pID: 1,
		//         pName: "Define Chart <strong>API</strong>",
		//         pStart: "2017-02-25",
		//         pEnd: "2017-03-17",
		//         pPlanStart: "2017-04-01",
		//         pPlanEnd: "2017-04-15 12:00",
		//         pClass: "ggroupblack",
		//         pLink: "",
		//         pMile: 0,
		//         pRes: "Brian",
		//         pComp: 0,
		//         pGroup: 1,
		//         pParent: 0,
		//         pOpen: 1,
		//         pDepend: "",
		//         pCaption: "",
		//         pCost: 1000,
		//         pNotes: "Some Notes text",
		//         category: "My Category",
		//         sector: "Finance"
		//     },
		//     {
		//         pID: 2,
		//         pName: "Define Chart <strong>API</strong>",
		//         pStart: "2017-02-25",
		//         pEnd: "2017-03-17",
		//         pPlanStart: "2017-04-01",
		//         pPlanEnd: "2017-04-15 12:00",
		//         pClass: "ggroupblack",
		//         pLink: "",
		//         pMile: 0,
		//         pRes: "Brian",
		//         pComp: 0,
		//         pGroup: 1,
		//         pParent: 0,
		//         pOpen: 1,
		//         pDepend: "",
		//         pCaption: "",
		//         pCost: 1000,
		//         pNotes: "Some Notes text",
		//         category: "My Category",
		//         sector: "Finance"
		//     }
		// );
	
		ganttChartElement.Draw();
	}
});