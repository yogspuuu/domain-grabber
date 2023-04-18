<?php
// Prompt the user to input a date to filter by
$dateToFilter = readline("Enter the date to filter by (format: Y-m-d | example 2023-04-14): ");

// Validate the format of the date
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateToFilter)) {
    die("Invalid date format. The date must be in the format Y-m-d.\n | example 2023-04-14");
}

// Loop through each page number
for ($pageNumber = 1; $pageNumber <= 40; $pageNumber++) {
    // Create the URL to scrape
    $url = "https://www.thesiterank.com/newly-registered-domain-names-by-date/$dateToFilter/$pageNumber";

    // Make a request to the URL and get the HTML content
    $htmlContent = file_get_contents($url);

    // Create a new DOMDocument object and load the HTML content
    $dom = new DOMDocument();
    @$dom->loadHTML($htmlContent);

    // Find all the <li class="col-md-4"><a href="...">...</a></li> elements
    $liElements = $dom->getElementsByTagName('li');

    // Check if data exists
    if ($liElements->length <= 37) {
        die("The data for the date specified, which is $dateToFilter, was not found. Only data from the past three days will be displayed.");
    }

    // Open the text file for writing
    $file = fopen("domain_names_$pageNumber.txt", 'w');

    // Loop through each <li> element and get the URL
    foreach ($liElements as $li) {
        // Check if the <li> element has the class "col-md-4"
        if ($li->getAttribute('class') === 'col-md-4') {
            // Find the <a> element inside the <li>
            $a = $li->getElementsByTagName('a')->item(0);

            // Get the URL from the "href" attribute of the <a> element and remove "/stats/?domain="
            $url = str_replace('/stats/?domain=', '', $a->getAttribute('href'));

            // Write the URL to the text file if it's not empty
            if (!empty($url)) {
                fwrite($file, $url . "\n");
            }
        }
    }

    // Close the text file
    fclose($file);

    echo "The URLs from page $pageNumber were successfully saved to domain_names_$pageNumber.txt\n";
}
