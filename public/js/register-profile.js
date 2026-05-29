(function () {
    const options = window.crazyExamProfileOptions || {};
    const classYears = options.classYears || {};
    const citySuggestions = options.citySuggestions || {};

    const schoolLevel = document.getElementById('school_level');
    const classYear = document.getElementById('class_year');
    const country = document.getElementById('country_of_study');
    const cityOptions = document.getElementById('city-options');

    if (!schoolLevel || !classYear || !country || !cityOptions) {
        return;
    }

    const initialClassYear = classYear.dataset.current || '';

    function addOption(select, value, label) {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = label;
        select.appendChild(option);
    }

    function refreshClassYears(preferredValue) {
        const values = classYears[schoolLevel.value] || [];

        classYear.innerHTML = '';
        addOption(classYear, '', schoolLevel.value ? 'Choose class / year' : 'Choose school level first');

        values.forEach((value) => addOption(classYear, value, value));

        if (preferredValue && !values.includes(preferredValue)) {
            addOption(classYear, preferredValue, preferredValue);
        }

        classYear.value = preferredValue || '';
        classYear.disabled = !schoolLevel.value;
    }

    function refreshCitySuggestions() {
        const values = citySuggestions[country.value] || [];

        cityOptions.innerHTML = '';
        values.forEach((value) => {
            const option = document.createElement('option');
            option.value = value;
            cityOptions.appendChild(option);
        });
    }

    refreshClassYears(initialClassYear);
    refreshCitySuggestions();

    schoolLevel.addEventListener('change', () => refreshClassYears(''));
    country.addEventListener('change', refreshCitySuggestions);
})();
