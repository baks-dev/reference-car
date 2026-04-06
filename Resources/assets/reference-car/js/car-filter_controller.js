const form = document.getElementById("carFilterForm");
const car_filter_form_brand = document.getElementById("car_filter_form_brand");
const car_filter_form_model = document.getElementById("car_filter_form_model");
const car_filter_form_model_petrol = document.getElementById("car_filter_form_model_petrol");

const updateForm = async (data, url, method) =>
{
    const req = await fetch(url, {
        method : method,
        body : data,
        headers : {
            "Content-Type" : "application/x-www-form-urlencoded",
            "charset" : "utf-8",
        },
    });

    return await req.text();
};

const parseTextToHtml = (text) =>
{
    const parser = new DOMParser();
    return parser.parseFromString(text, "text/html");
};

const handleBrandChange = async (e) =>
{
    const formData = new FormData();
    formData.append("brand", e.target.value);

    try
    {
        const response = await fetch(form.action, {
            method : "POST",
            body : new URLSearchParams(formData),
            headers : {
                "Content-Type" : "application/x-www-form-urlencoded",
                "X-Requested-With" : "XMLHttpRequest",
            },
        });

        if(!response.ok)
        {
            throw new Error("Network error");
        }

        const html = await response.text();
        const doc = parseTextToHtml(html);

        // Обновляем модель
        const newModelSelect = doc.getElementById("car_filter_form_model");
        if(newModelSelect)
        {
            car_filter_form_model.innerHTML = newModelSelect.innerHTML;
            car_filter_form_model.disabled = newModelSelect.disabled;

            // Сбрасываем модификацию при изменении марки
            car_filter_form_model_petrol.innerHTML = "<option value=\"\">Сначала выберите модель</option>";
            car_filter_form_model_petrol.disabled = true;
        }
    }
    catch(error)
    {
        console.error("Error:", error);
    }
};

const handleModelChange = async (e) =>
{
    const formData = new FormData();
    formData.append("brand", car_filter_form_brand.value);
    formData.append("model", e.target.value);

    try
    {
        const response = await fetch(form.action, {
            method : "POST",
            body : new URLSearchParams(formData),
            headers : {
                "Content-Type" : "application/x-www-form-urlencoded",
                "X-Requested-With" : "XMLHttpRequest",
            },
        });

        if(!response.ok)
        {
            throw new Error("Network error");
        }

        const html = await response.text();
        const doc = parseTextToHtml(html);

        // Обновляем модификацию
        const newPetrolSelect = doc.getElementById("car_filter_form_model_petrol");
        if(newPetrolSelect)
        {
            car_filter_form_model_petrol.innerHTML = newPetrolSelect.innerHTML;
            car_filter_form_model_petrol.disabled = newPetrolSelect.disabled;
        }
    }
    catch(error)
    {
        console.error("Error:", error);
    }
};

// Навешиваем обработчики событий
car_filter_form_brand.addEventListener("change", handleBrandChange);
car_filter_form_model.addEventListener("change", handleModelChange);