const $ = require("jquery");

// Gérer la preview
$(".image-file").on('change', (e) => {
    const file = e.currentTarget.files[0];

    if(file)
    {
        const reader = new FileReader();
        reader.onload = e => {
            const content = e.target.result;
            $("#preview").attr("src", content);
        };
        reader.readAsDataURL(file);
    }
});