

window.onclick = function(event) {
    if (event.target === document.getElementById("myModal")) {
        document.getElementById("myModal").style.display = "none";
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("myModal");
    var modalH = document.getElementById("modalheader");
    var modalFP =document.getElementById("modal-from-p");
    var modalTP = document.getElementById("modal-to-p");
    var modalFD = document.getElementById("modal-from-day");
    var modalTD = document.getElementById("modal-to-day");
    var modalComment = document.getElementById("modal-comment-div");
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            start: 'title',
            center: 'prevYear,nextYear',
            end: 'today prev,next',
        },
        initialView: 'dayGridMonth',
        events: items,
        dayMaxEventRows: true,

        eventClick: function(info) {
            var fromDate = new Date(info.event.start.toISOString());
            var fromWeekday = fromDate.toLocaleString("default", {weekday: "long"});
            var toWeekday;
            var toDate;
            if(info.event.end === null){
                toDate = new Date(info.event.start.toISOString());
            }
            else {
                toDate = new Date(info.event.end.toISOString());

            }
                toWeekday = toDate.toLocaleString("default", {weekday: "long"});
                modalFP.innerHTML = fromDate.getFullYear() + '-' + (fromDate.getMonth() + 1) + '-' + fromDate.getDate();
                modalTP.innerHTML = toDate.getFullYear() + '-' + (toDate.getMonth() + 1) + '-' + toDate.getDate();
                modalFD.innerHTML = fromWeekday;
                modalTD.innerHTML = toWeekday;
                modalH.innerHTML = info.event.title;
                modalComment.innerHTML = info.event.extendedProps.description;
                modal.style.display = "block";
        }

    });
    calendar.render();
});
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("myModal");
    var modalH = document.getElementById("modalheader");
    var modalFP =document.getElementById("modal-from-p");
    var modalTP = document.getElementById("modal-to-p");
    var modalFD = document.getElementById("modal-from-day");
    var modalTD = document.getElementById("modal-to-day");
    var modalComment = document.getElementById("modal-comment-div");
    var calendarElist = document.getElementById('list');
    var calendarlist = new FullCalendar.Calendar(calendarElist, {

        initialView: 'listWeek',
        events: items,
        //dayMaxEventRows: true,
        eventClick: function(info) {

            var fromDate = new Date(info.event.start.toISOString());
            var fromWeekday = fromDate.toLocaleString("default", {weekday: "long"});
            var toWeekday;
            var toDate;
            if(info.event.end === null){
                toDate = new Date(info.event.start.toISOString());
            }
            else {
                toDate = new Date(info.event.end.toISOString());

            }
            toWeekday = toDate.toLocaleString("default", {weekday: "long"});
            modalFP.innerHTML = fromDate.getFullYear() + '-' + (fromDate.getMonth() + 1) + '-' + fromDate.getDate();
            modalTP.innerHTML = toDate.getFullYear() + '-' + (toDate.getMonth() + 1) + '-' + toDate.getDate();
            modalFD.innerHTML = fromWeekday;
            modalTD.innerHTML = toWeekday;
            modalH.innerHTML = info.event.title;
            modalComment.innerHTML = info.event.extendedProps.description;
            modal.style.display = "block";

        }

    });
    calendarlist.render();
});

