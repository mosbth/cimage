/**
 * JavaScript utilities for CImage and img.php.
 */
window.CImage = (function() {
    "use strict";


    /**
     * Waiting for ECMA 6...
     */
     var forEach = function(array, callback, scope) {
      for (var i = 0; i < array.length; i++) {
        callback.call(scope, i, array[i]);
      }
    };



    /**
     * Update the permalink.
     */
    function updatePermaLink() {
        var link,
            input1 = document.getElementById("input1"),
            input2 = document.getElementById("input2"),
            input3 = document.getElementById("input3"),
            input4 = document.getElementById("input4"),
            input5 = document.getElementById("input5"),
            input6 = document.getElementById("input6"),
            details = document.getElementById("viewDetails"),
            stack = document.getElementById("stack"),
            bg = document.getElementById("bg"),
            permalink = document.getElementById("permalink");
            
        link  = "?";
        link += "input1=" + encodeURIComponent(input1.value) + "&";
        link += "input2=" + encodeURIComponent(input2.value) + "&";
        link += "input3=" + encodeURIComponent(input3.value) + "&";
        link += "input4=" + encodeURIComponent(input4.value) + "&";
        link += "input5=" + encodeURIComponent(input5.value) + "&";
        link += "input6=" + encodeURIComponent(input6.value) + "&";
        link += "json=" + encodeURIComponent(details.checked) + "&";
        link += "stack=" + encodeURIComponent(stack.checked) + "&";
        link += "bg=" + encodeURIComponent(bg.checked);
        permalink.href = link;
    }



    /**
     * Init the compare page with details.
     */
    function compareLoadImage(event) {
        var img, json, button, area, deck, id, permalink;

        id = this.dataset.id;
        img = document.getElementById("img" + id);
        json = document.getElementById("json" + id);
        button = document.getElementById("button" + id);
        area = document.getElementById("area" + id);
        deck = document.getElementById("deck" + id);
        
        updatePermaLink();
        
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
            json.innerHTML = "filename: " + data.filename + "\ncolors: " + data.colors + "\nsize: " + data.size + "\nwidth: " + data.width + "\nheigh: " + data.height + "\naspect-ratio: " + data.aspectRatio + "\npng-type: " + data.pngType;
        })
            .fail(function() {
                json.innerHTML = "Details not available."
                console.log( "JSON error" );
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
            input5 = document.getElementById("input5"),
            input6 = document.getElementById("input6"),
            details = document.getElementById("viewDetails"),
            stack = document.getElementById("stack"),
            bg = document.getElementById("bg"),
            buttons = document.getElementById("buttonWrap");

        input1.addEventListener("change", compareLoadImage);
        input2.addEventListener("change", compareLoadImage);
        input3.addEventListener("change", compareLoadImage);
        input4.addEventListener("change", compareLoadImage);
        input5.addEventListener("change", compareLoadImage);
        input6.addEventListener("change", compareLoadImage);

        // Toggle json
        details.addEventListener("change", function() {
            var elements = document.querySelectorAll(".json");
            
            forEach(elements, function (index, element) {
                element.classList.toggle("hidden");
            });

            /* ECMA 6
            for (var element of elements) {
                element.classList.toggle("hidden");
            }
            */
            
            updatePermaLink();
            console.log("View JSON");
        });

        // Show json as default
        if (options.json === true) {
            details.setAttribute("checked", "checked");
            myEvent = new CustomEvent("change");
            details.dispatchEvent(myEvent);
        }

        // Toggle background color
        bg.addEventListener("change", function() {
            var elements = document.querySelectorAll(".area");

            forEach(elements, function (index, element) {
                element.classList.toggle("invert");
            });
        });

        // Check background
        if (options.bg === true) {
            bg.setAttribute("checked", "checked");
            bg.classList.toggle("invert");
            myEvent = new CustomEvent("change");
            bg.dispatchEvent(myEvent);
        }

        // Toggle stack
        stack.addEventListener("change", function() {
            var element,
                elements = document.querySelectorAll(".area");

            buttons.classList.toggle("hidden");

            forEach(elements, function (index, element) {
                element.classList.toggle("stack");

                if (!element.classList.contains('hidden')) {
                    onTop = element;
                }
            });

            /* ECMA 6
            for (element of elements) {
                element.classList.toggle("stack");

                if (!element.classList.contains('hidden')) {
                    onTop = element;
                }
            }
            */

            onTop.classList.toggle("top");
            updatePermaLink();

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

        forEach(elements, function (index, element) {
            element.addEventListener("click", function() {
                var id = this.dataset.id,
                    area = document.getElementById("area" + id);

                area.classList.toggle("top");
                onTop.classList.toggle("top");
                onTop = area;
                console.log("button" + id);
            });
        });

        /* ECMA 6
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
        */

        input1.value = options.input1 || null;
        input2.value = options.input2 || null;
        input3.value = options.input3 || null;
        input4.value = options.input4 || null;
        input5.value = options.input5 || null;
        input6.value = options.input6 || null;

        compareLoadImage.call(input1);
        compareLoadImage.call(input2);
        compareLoadImage.call(input3);
        compareLoadImage.call(input4);
        compareLoadImage.call(input5);
        compareLoadImage.call(input6);

        console.log(options);
    } 


    return {
        "compare": compareInit
    };

}());
