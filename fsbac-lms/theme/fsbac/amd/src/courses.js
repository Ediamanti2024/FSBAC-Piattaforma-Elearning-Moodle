
import $ from 'jquery';
import * as Filters from 'theme_fsbac/filters';
import * as Templates from 'core/templates';

let OFFSET_PAGINATION = 12;

const EMPTY_VALUE = '-';
let courses = [];
let coursesFilters = [];
let currentPage = 1;


const TYPE_FILTER = {
    ORDER: 'order',
    TEXT: 'text',
    TAG: 'tag',
    PROGRAM: 'program',
    TEACHERS: 'teachers',
    TYPE: 'type',
    TIME: 'time',
    PATHS: 'paths',
    LEVEL: 'level',
    DURATION: 'duration',
    CERTICATE: 'certificate'
};

let filterSet = {}

const setPagination = () => {
    const total = coursesFilters.length;
    if (total !== 0 && total > OFFSET_PAGINATION) {

        const numPages = Math.ceil(total / OFFSET_PAGINATION);

        const pages = [];
        for (let i = 1; i <= numPages; i++) {
            pages.push({
                active: i === currentPage,
                page: i
            });
        }
        const disabledNext = currentPage === numPages;
        const disabledPrev = currentPage === 1;
        const container = $('.fsbac-pagination-container');
        Templates.render(`theme_fsbac/components/pagination/index`, { pages, disabledNext, disabledPrev }).then((html, js) => {
            Templates.replaceNodeContents(container, html, js);
        });
    } else {
        $('.fsbac-pagination-container').hide();
    }

    setViewCourses();
};

const setViewCourses = () => {

    const start = ((currentPage - 1) * OFFSET_PAGINATION);
    const end = currentPage * OFFSET_PAGINATION;
    const courses = coursesFilters.slice(start, end);
    const isPath = $('#page-theme-fsbac-pages-paths').length > 0;
    const container = $('.fsbac-cards-courses');

    const tmp = isPath ? `theme_fsbac/pages/paths/view` : `theme_fsbac/pages/courses/view`;
    Templates.render(tmp, { courses }).then((html, js) => {
        Templates.replaceNodeContents(container, html, js);
    });
};

const setFilter = (type, value) => {

    filterSet = {
        ...filterSet,
        [type]: value
    };

    if (!value || value === EMPTY_VALUE) {
        delete filterSet[type];
    }

    let courses = [...c];

    Object.keys(filterSet).forEach(key => {
        const value = filterSet[key];
        courses = applyFilter(key, value, courses)
    })

    coursesFilters = [...courses];
}
const applyFilter = (type, value, coursesNew) => {
    //let coursesNew = [...c];
    if (type === TYPE_FILTER.ORDER) {
        const values = filtersByOrder(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.TAG) {
        const values = filtersByTag(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.PROGRAM) {
        const values = filtersByProgram(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }
    if (type === TYPE_FILTER.TEACHERS) {
        const values = filtersByTeachers(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.TYPE) {
        const values = filtersByType(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.TIME) {
        const values = filtersByTime(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.PATHS) {
        const values = filtersByPath(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.LEVEL) {
        const values = filtersByLevel(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }
    if (type === TYPE_FILTER.DURATION) {
        const values = filtersByDuration(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }

    if (type === TYPE_FILTER.CERTICATE) {
        const values = filtersByCerticate(coursesNew, value);
        if (values) {
            coursesNew = [...values];
        }
    }




    if (type === TYPE_FILTER.TEXT && value) {
        coursesNew = [...filtersByText(coursesNew, value)];
    }

    return [...coursesNew];
}

const filterSelect = (e) => {
    const current$ = $(e.currentTarget);
    const type = current$.data('type');
    const elmType = $(e.currentTarget).attr('type');
    let value = current$.val();
    if (elmType === 'checkbox') {
        value = $(e.currentTarget).prop('checked');
    }

    setFilter(type, value)
};



const filtersByText = (courses, value) => {
    const rg = new RegExp(value, 'gi');
    return courses.filter(({ fullname }) => fullname.search(rg) !== -1);
};

const filtersByProgram = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    const rg = new RegExp(value, 'gi');
    return courses.filter(({ programma }) => (programma || '').search(rg) !== -1);
};

const filtersByTeachers = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    const rg = new RegExp(value, 'gi');
    return courses.filter(({ autore }) => (autore || '').search(rg) !== -1);
};

const filtersByTag = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    return courses.filter(({ tags }) => tags.some(tag => tag === value));
};

const filtersByPath = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    return courses.filter(({ paths }) => paths.some(p => p.toString() === value));
};

const filtersByTime = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    return courses.filter(({ enddate_fascia }) => enddate_fascia === value);
};

const filtersByType = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    return courses.filter(({ tipologia }) => tipologia === value);
};

const filtersByLevel = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    return courses.filter(({ livello }) => livello === value);
};

const filtersByDuration = (courses, value) => {
    return courses.filter(({ duration }) => duration >= value.start && duration <= value.end);
};

const filtersByCerticate = (courses, value) => {

    return value ? courses.filter(({ isCertificate }) => isCertificate) : courses

};


const filtersByOrder = (courses, value) => {
    if (value === EMPTY_VALUE) {
        return null;
    }
    if (value === 'date_start') {
        return courses.sort(function (a, b) { return a.startdate - b.startdate; });
    }

    if (value === 'a_z') {
        return courses.sort((a, b) => a.fullname.localeCompare(b.fullname, 'es', { sensitivity: 'base' }));
    }
    if (value === 'z_a') {
        return courses.sort((a, b) => a.fullname.localeCompare(b.fullname, 'es', { sensitivity: 'base' })).reverse();
    }
    return null;
};
const submitFilter = () => {
    $('.fsbac-cards-courses').show();
    $('.fsbac-empty-values').hide();
    Filters.closeFilter();
    setPagination();
    if (coursesFilters.length === 0) {
        $('.fsbac-empty-values').show();
        $('.fsbac-cards-courses').hide();
        return;
    }
}

export const init = (c, config) => {
    const { offset, params } = config || {};
    if (offset) { OFFSET_PAGINATION = offset; }
    courses = c;
    window.c = courses;
    coursesFilters = [...c];
    Filters.initEvent();

    Filters.initFilterSliderRange('slider-1', 'slider-2', (value) => {

        setFilter(TYPE_FILTER.DURATION, value);

    });
    $('.fsbac-select-fiters').on('change', 'select', filterSelect);

    $('.fsbac-filters-input-search').on('keyup', 'input', filterSelect);

    $('.fsbac-filters-input-search').on('keyup', 'input', (event) => {
        if (event.key.charCodeAt() === 69) {
            submitFilter();
        }
    });

    $('.fsbac-filters-checkbox').on('change', 'input', filterSelect);

    $('.fsbac-filters-input-search').on('click', '.submit-filter', () => {
        submitFilter();
    });
    $('.fsbac-pagination-container').on('click', '.fsbac-button-pagination', (e) => {
        const $current = $(e.currentTarget);
        const page = $current.data('page');

        if (page === 'next') {
            currentPage = currentPage + 1;
        }
        if (page === 'prev') {
            currentPage = currentPage - 1;
        }
        if (page !== 'prev' && page !== 'next') {
            currentPage = page;
        }
        setPagination();
    });

    if (params) {
        Object.keys(params).map((key) => {
            const value = params[key];
            $(`.form-group #${key}`).val(value)
            setFilter(key, value);
        })
    }

    setPagination()

};