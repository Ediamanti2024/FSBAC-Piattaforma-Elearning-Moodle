
import $ from 'jquery';

const DOM = {
    BUTTON_SHOW: '.showFilters',
    CONTAINER_INPUTS: '.fsbac-filters-container-inputs'
};

export const initEvent = () => {

    $(DOM.BUTTON_SHOW).on('click', () => {
        if ($(DOM.CONTAINER_INPUTS).hasClass('show')) {
            $(DOM.CONTAINER_INPUTS).removeClass('show');
            $('.fsbac-filters-container').css('z-index', 0);

        } else {
            $(DOM.CONTAINER_INPUTS).addClass('show');
            $('.fsbac-filters-container').css('z-index', 2000);
        }


    });
};

export const closeFilter = () => {
    $(DOM.CONTAINER_INPUTS).removeClass('show');
};

export const initFilterSliderRange = (idSlider1, idSlider2, onChange) => {
    const sliderOne = document.getElementById(idSlider1);
    const sliderOneBubble = document.getElementById(`${idSlider1}-bubble`);
    const sliderTwo = document.getElementById(idSlider2);
    const sliderTwoBubble = document.getElementById(`${idSlider2}-bubble`);
    const sliderTrack = document.querySelector(".slider-track");
    const minGap = 0;

    if (!sliderOne || !sliderTwo) return;

    const sliderMaxValue = document.getElementById(idSlider1).max;
    function fillColor() {
        const percent1 = (sliderOne.value / sliderMaxValue) * 100;
        const percent2 = (sliderTwo.value / sliderMaxValue) * 100;
        sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% ,#7E65A0 ${percent1}% , #7E65A0 ${percent2}%, #dadae5 ${percent2}%)`;
    };

    fillColor();

    function slideOne() {
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
            sliderOne.value = parseInt(sliderTwo.value) - minGap;
        }
        fillColor();
        setBubble(sliderOne, sliderOneBubble);

        onChange({
            start: sliderOne.value,
            end: sliderTwo.value
        });
    }
    function slideTwo() {
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
            sliderTwo.value = parseInt(sliderOne.value) + minGap;
        }
        fillColor();
        setBubble(sliderTwo, sliderTwoBubble);
        onChange({
            start: sliderOne.value,
            end: sliderTwo.value
        });
    }

    function setBubble(range, bubble) {
        const val = range.value;
        const min = range.min ? range.min : 0;
        const max = range.max ? range.max : 100;
        const newVal = Number(((val - min) * 100) / (max - min));
        if (val < 60) {
            bubble.innerHTML = `${val}min`;
        } else {
            const h = parseInt(val / 60);
            bubble.innerHTML = `${h}h`;
        }


        // Sorta magic numbers based on size of the native UI thumb
        bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
    }

    setTimeout(() => {
        $('body').on('input', `#${idSlider1}`, () => {
            slideOne();
        });
        $('body').on('input', `#${idSlider2}`, () => {
            slideTwo();
        });
        slideOne();
        slideTwo();
    }, 1000)



};





