<?php
/**
 * Plugin Name: Speachtotext
 * Plugin URI: https://github.com/drajathasan/slims-speach-to-text
 * Description: Plugin untuk mengkonversi suara ke teks
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/
 * Original Source Code : https://github.com/DKMitt/speech-to-text-js
 */

// get plugin instance
$plugin = \SLiMS\Plugins::getInstance();

$plugin->register('after_content_load', function($opac){
    $opac->js = <<<HTML
        <script>
            // Speach to text
            try {
                var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                var recognition = new SpeechRecognition();
            } catch (e) {
                console.error(e);
            }

            // If false, the recording will stop after a few seconds of silence.
            // When true, the silence period is longer (about 15 seconds),
            // allowing us to keep recording even when the user pauses. 
            recognition.continuous = true;

            // This block is called every time the Speech APi captures a line. 
            recognition.onresult = function(event) {

                document.querySelector('#search-input').value = 'Menunggu kata kunci';
                // event is a SpeechRecognitionEvent object.
                // It holds all the lines we have captured so far. 
                // We only need the current one.
                var current = event.resultIndex;

                // Get a transcript of what was said.
                var transcript = event.results[current][0].transcript;

                // Add the current transcript to the contents of our Note.
                // There is a weird bug on mobile, where everything is repeated twice.
                // There is no official solution so far so we have to handle an edge case.
                var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

                if (!mobileRepeatBug) {
                    if (transcript.match(/(tolong)|(Tolong)|(carikan)|(carikan)|(buku)|(slims)/g))
                    {
                        document.querySelector('#search-input').value = 'Tunggu sebentar';
                        document.querySelector('#search-input').value = transcript.replace(/(tolong)|(buku)|(Tolong)|(carikan)|(carikan)|(slims)/g, '');
                        document.querySelector('.card-body > form').submit();
                    }
                    else
                    {
                        console.log(transcript)
                    }
                }
                else
                {
                    document.querySelector('#search-input').value = 'Gawa anda tidak mendukung : (.';
                }
            };

            document.addEventListener("DOMContentLoaded", function() {
                recognition.start();
            });
        </script>
    HTML;
});
