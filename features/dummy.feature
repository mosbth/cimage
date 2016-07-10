Feature: dummy
    Display an dummy image without using an existing image on file.

    Scenario: Set source to be dummy
        Given Set src "dummy"
        When Get image
        Then Returns status code "200"
        And Compares to image "dummy"
