/**
 * JavaScript utilities for CImage and img.php.
 */
window.CImage = (function(){
    "use strict";


    /**
     * Init the compare page with details.
     */
    function compareLoadImage(event) {
        var img, json, button, area, deck, id;

        id = this.dataset.id;
        img = document.getElementById("img" + id);
        json = document.getElementById("json" + id);
        button = document.getElementById("button" + id);
        area = document.getElementById("area" + id);
        deck = document.getElementById("deck" + id);

        if (this.value == "") {
            // Clear image if input is cleared
            button.setAttribute("disabled", "disabled");
            area.classList.add("hidden");
            button.classList.remove("selected");
            return;
        }

        // Display image in its area
        img.src = this.value;
        area.classList.remove("hidden");

        $.getJSON(this.value + "&json", function(data) {
            json.innerHTML = "filename: " + data.filename + "\ncolors: " + data.colors + "\nsize: " + data.size + "\nwidth: " + data.width + "\nheigh: " + data.height + "\naspect-ratio: " + data.aspectRatio;
        });

        // Display image in overlay
        button.removeAttribute("disabled");
        button.classList.add("selected");


    };



    /**
     * Init the compare page with details.
     */
    function compareInit(options) 
    {
        var elements, id, onTop, myEvent,
            input1 = document.getElementById("input1"),
            input2 = document.getElementById("input2"),
            input3 = document.getElementById("input3"),
            input4 = document.getElementById("input4"),
            details = document.getElementById("viewDetails"),
            stack = document.getElementById("stack"),
            buttons = document.getElementById("buttonWrap");

/*            img1 = document.getElementById("img1"),
            img2 = document.getElementById("img2"),
            img3 = document.getElementById("img3"),
            img4 = document.getElementById("img4"),
            img01 = document.getElementById("img01"),
            img02 = document.getElementById("img02"),
            img03 = document.getElementById("img03"),
            img04 = document.getElementById("img04"),
            json1 = document.getElementById("json1"),
            json2 = document.getElementById("json2"),
            json3 = document.getElementById("json3"),
            json4 = document.getElementById("json4"),
            json01 = document.getElementById("json01"),
            json02 = document.getElementById("json02"),
            json03 = document.getElementById("json03"),
            json04 = document.getElementById("json04"),
            button1 = document.getElementById("button1"),
            button2 = document.getElementById("button2"),
            button3 = document.getElementById("button3"),
            button4 = document.getElementById("button4"),
            area1 = document.getElementById("area1"),
            area2 = document.getElementById("area2"),
            area3 = document.getElementById("area3"),
            area4 = document.getElementById("area4");*/

        input1.addEventListener("change", compareLoadImage);
        input2.addEventListener("change", compareLoadImage);
        input3.addEventListener("change", compareLoadImage);
        input4.addEventListener("change", compareLoadImage);

        // Toggle json
        details.addEventListener("change", function() {
            var elements = document.querySelectorAll(".json");
            for (var element of elements) {
                element.classList.toggle("hidden");
            }
        });

        // Do not show json as default
        if (options.json === false) {
            details.setAttribute("checked", "checked");
            myEvent = new CustomEvent("change");
            details.dispatchEvent(myEvent);
        }

        // Toggle stack
        stack.addEventListener("change", function() {
            var element,
                elements = document.querySelectorAll(".area");

            buttons.classList.toggle("hidden");

            for (element of elements) {
                element.classList.toggle("stack");

                if (!element.classList.contains('hidden')) {
                    onTop = element;
                }
            }
            onTop.classList.toggle("top");

            console.log("Stacking");
        });

        // Stack as default
        if (options.stack === true) {
            stack.setAttribute("checked", "checked");
            myEvent = new CustomEvent("change");
            stack.dispatchEvent(myEvent);
        }

        // Button clicks
        elements = document.querySelectorAll(".button");
        for (var element of elements) {
            element.addEventListener("click", function() {
                var id = this.dataset.id,
                    area = document.getElementById("area" + id);
                
                area.classList.toggle("top");
                onTop.classList.toggle("top");
                onTop = area;
                console.log("button" + id);
            });
        }

        input1.value = options.input1 || null;
        input2.value = options.input2 || null;
        input3.value = options.input3 || null;
        input4.value = options.input4 || null;

        compareLoadImage.call(input1);
        compareLoadImage.call(input2);
        compareLoadImage.call(input3);
        compareLoadImage.call(input4);

        console.log(options);
    } 


    return {
        "compare": compareInit
    };

}());