//making statistic tabs alive
var $wrapper = jQuery('#multiForm'),
    $allTabs = $wrapper.find('.stat-page > div'),
    $tabMenu = $wrapper.find('.stat-menu li'),
    $line = jQuery('<div class="line"></div>').appendTo($tabMenu);

$allTabs.not(':first-of-type').hide();
$tabMenu.filter(':first-of-type').find(':first').width('100%')

$tabMenu.each(function (i) {
    jQuery(this).attr('data-stat', 'stat' + i);
});

$allTabs.each(function (i) {
    jQuery(this).attr('data-stat', 'stat' + i);
});

$tabMenu.on('click', function () {

    var statisticsPage = jQuery(this).data('stat'),
        $getWrapper = jQuery(this).closest($wrapper);

    $getWrapper.find($tabMenu).removeClass('active');
    jQuery(this).addClass('active');

    $getWrapper.find('.line').width(0);
    jQuery(this).find($line).animate({'width': '100%'}, 'fast');
    $getWrapper.find($allTabs).hide();
    $getWrapper.find($allTabs).filter('[data-stat=' + statisticsPage + ']').show();
});

//Ajax get data for remote to absence bar
var test;

var aCount;
var rCount;
jQuery.ajax({
    url: ajaxurl,
    async: false,
    type: 'POST',
    data : {
        "action" : 'worktmp_absence_overall_count', "absence_type": 'absence'},
    success: function(response){
        aCount = response;
    }
});
jQuery.ajax({
    url: ajaxurl,
    async: false,
    type: 'POST',
    data : {
        "action" : 'worktmp_absence_overall_count', "absence_type": 'remote' },
    success: function(response){
        rCount = response;
    }
});

//Ajax get data for radar bar
var absenceMonth;
jQuery.ajax({
    url: ajaxurl,
    async: false,
    type: 'POST',
    data : {
        "action" : 'worktmp_absence_type_to_month', "absence_type": 'Absence' },
    success: function(response){
        absenceMonth = JSON.parse(response);
    }
});
var remoteMonth;
jQuery.ajax({
    url: ajaxurl,
    async: false,
    type: 'POST',
    data : {
        "action" : 'worktmp_absence_type_to_month', "absence_type": 'Remote work' },
    success: function(response){
        remoteMonth = JSON.parse(response);
    }
});

//config, labels for charts
const labelsBar = [
    'Absence',
    'Remote Work',
];
const dataBar = {
    labels: labelsBar,
    datasets: [{
        label: 'Absence Type',
        backgroundColor: [
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)'
        ],
        borderColor: [
            'rgb(99,232,255)',
        ],
        borderWidth: 1,

        data: [aCount , rCount],
    }]
};
const configBar = {
    type: 'bar',
    data: dataBar,
    options: {}
};
const dataRadar = {
    labels: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ],
    datasets: [{
        label: 'Absence',
        data: absenceMonth,
        fill: true,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgb(255, 99, 132)',
        pointBackgroundColor: 'rgb(255, 99, 132)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgb(255, 99, 132)'
    }, {
        label: 'Remote Work',
        data: remoteMonth,
        fill: true,
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgb(54, 162, 235)',
        pointBackgroundColor: 'rgb(54, 162, 235)',
        pointBorderColor: '#fff',
        pointHoverBackgroundColor: '#fff',
        pointHoverBorderColor: 'rgb(54, 162, 235)'
    }]
};
const configRadar = {
    type: 'radar',
    data: dataRadar,
    options: {
        elements: {
            line: {
                borderWidth: 3
            }
        }
    },
};


const type_month_radar_chart = new Chart(
    document.getElementById('type_month_radar_chart'),
    configRadar
);
const absence_to_remote = new Chart(
    document.getElementById('absence_to_remote'),
    configBar
);
//disable non working days for counter
function disableDaysFromCounter(year, month, day) {
    var workDay = new Date(year, month, day).getDay();
    return workDay !==0 && workDay !==6;
}
jQuery("#worktmp_statistic_button").on("click", function() {
    var userName = jQuery("#searchname").val();
    if(userName === "") alert("User Not Selected");
    else {
        jQuery("#selected_user_show").text(userName);
        jQuery("#statistic_header1").text("Stationary working days compared to absence and remote work");
        jQuery("#statistic_year_header").text("Absence to month through selected year");

        var chosenMonth = new Date(jQuery("#searchdate").val());
        //prepare date for php function format yyyy-mm
        var chosenMonthToPHP = new Date(chosenMonth).toISOString().slice(0, 7);
        var month = chosenMonth.getMonth();
        var year = chosenMonth.getFullYear();
        chosenMonth = new Date(year, month, 0).getDate();
        var workingDaysInMonth = 0;
        //count working days in month
        for (var i = 0; i < chosenMonth; i++) {
            if (disableDaysFromCounter(year, month, i + 1)) workingDaysInMonth++;
        }
        //counting absence in work in selcted month&year for selected user
        var absenceAtWork;
        jQuery.ajax({
            url: ajaxurl,
            async: false,
            type: 'POST',
            data: {
                "action": 'worktmp_absence_type_to_month_and_username',
                "user_name": userName,
                "month": chosenMonthToPHP,
                "absence_type": "Absence"
            },
            success: function (response) {
                absenceAtWork = response;

            }
        });
        //counting remote work in selected month&year for selected user
        var remoteWork;
        jQuery.ajax({
            url: ajaxurl,
            async: false,
            type: 'POST',
            data: {
                "action": 'worktmp_absence_type_to_month_and_username',
                "user_name": userName,
                "month": chosenMonthToPHP,
                "absence_type": "Remote work"
            },
            success: function (response) {
                remoteWork = response;
            }

        });
        var workingDays = workingDaysInMonth - remoteWork - absenceAtWork;
        // Pie Chart labels, set data
        const pieData = {
            labels: [
                'Remote Work',
                'Absence',
                'Stationary Work'
            ],
            datasets: [{
                label: 'Work Statistic',
                data: [remoteWork, absenceAtWork, workingDays],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54,235,105)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        };
        const pieConfig = {
            type: 'doughnut',
            data: pieData,
        };
        const absence_to_remote_to_normal = new Chart(
            document.getElementById('absence_to_remote_to_normal'),
            pieConfig,
        );

        var tableOfDatesPhp = [];
        for( var i = 1; i <= 12; i++) {
            tableOfDatesPhp.push(new Date(year,i).toISOString().slice(0, 7),);
        }

        //bar chart ajax
        var remoteYearCounter = [];
        var absenceYearCounter = [];
        jQuery.ajax({
            url: ajaxurl,
            async: false,
            type: 'POST',
            data: {
                "action": 'worktmp_absence_throught_year',
                "user_name": userName,
                "dates": tableOfDatesPhp,
                "absence_type": "Remote work"
            },
            success: function (response) {
                remoteYearCounter= JSON.parse(response);
            }

        });
        jQuery.ajax({
            url: ajaxurl,
            async: false,
            type: 'POST',
            data: {
                "action": 'worktmp_absence_throught_year',
                "user_name": userName,
                "dates": tableOfDatesPhp,
                "absence_type": "Absence"
            },
            success: function (response) {
                absenceYearCounter = JSON.parse(response);
            }

        });

        // Line chart labels, set data
        const lineData = {
            labels: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
                'July',
                'August',
                'September',
                'October',
                'November',
                'December'
            ],
            datasets: [{
                label: 'Remote Work',
                data: remoteYearCounter,
                fill: false,
                backgroundColor: 'rgb(255, 99, 132)',
            },
                {
                    label: 'Absence',
                    data: absenceYearCounter,
                    fill: false,
                    backgroundColor: 'rgb(54,235,105)',
                }
            ]
        };
        const lineConfig = {
            type: 'bar',
            data: lineData,
        };
        const statistic_year_user = new Chart(
            document.getElementById('statistic_year_user'),
            lineConfig,
        )
    }
});

