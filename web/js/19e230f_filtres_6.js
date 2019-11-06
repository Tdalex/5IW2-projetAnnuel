$(function () {
    $("#durations").ionRangeSlider({
        hide_min_max: true,
        keyboard: true,
        min: 0,
        max: 30,
        from: 10,
        to: 20,
        type: 'double',
        step: 1,
        suffix: "J",
        grid: true
    });

    $("#nbstops").ionRangeSlider({
        hide_min_max: true,
        keyboard: true,
        min: 0,
        max: 30,
        from: 10,
        to: 20,
        type: 'double',
        step: 1,
        suffix: "J",
        grid: true
    });
});

function submit() {
    document.getElementById("myForm").submit();
};