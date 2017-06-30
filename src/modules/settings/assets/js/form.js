/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
window.onload = init;

function init() {
    var targetElementTimeFrom = document.getElementById("ectopicTimeFrom");
    var targetElementTimeTo = document.getElementById("ectopicTimeTo");

    document.getElementById("w1").onchange = function() {
        drawEctopicTime(this, targetElementTimeFrom);
    };

    document.getElementById("w2").onchange = function() {
        drawEctopicTime(this, targetElementTimeTo);
    };

    var extraFeeWeekdays = document.getElementById("extraFeeWeekdays");
    document.getElementById("extraFeeDays").onchange = function() {
        changeWeekdays(this, extraFeeWeekdays);
    };
}

function drawEctopicTime(sourceElement, targetElement) {
    var sourceTime = sourceElement.value;
    if(/(\d\d)(?::)(\d\d)(?::)(\d\d)/.test(sourceTime)) {
        var utcDate = moment(sourceTime + " +0000", "h:mm:ss A Z");
        targetElement.innerHTML = utcDate.tz("America/Chicago").format("h:mm:ss A");
    }
}

function changeWeekdays(source, target) {
    var selected = (source.options.selectedIndex !== -1);
    if(selected && !target.disabled) {
        for(var i in target.options) {
            target.options[i].selected = false;
        }
        target.disabled = true;
    }
    else if(!selected && target.disabled) {
        target.disabled = false;
    }
}