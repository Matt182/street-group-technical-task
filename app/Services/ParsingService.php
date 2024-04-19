<?php

namespace App\Services;

class ParsingService
{
    /**
     * Take a set of name(s) and separate into individual components
     * 
     * @param array $entries
     * @return array
     */
    public function parseNames(array $entries): array
    {
        //Remove the first entry in the array as this is the header
        array_shift($entries);

        $parsedData = [];
        foreach($entries as $entry) {
            //Remove unwanted chars or text from each entry
            $cleanedEntry = $this->removeTextFromString($entry, ["'", ","]);

            //Split each entry into individual people
            $people = $this->splitIntoPeople($cleanedEntry);

            if (is_array($people)) {
                //More than 1 person is present so we need to compare details for required fields which may be missing
                $firstPerson = $this->populateDetails($people[0]);
                $secondPerson = $this->populateDetails($people[1]);

                if (!$firstPerson['last_name']) {
                    $firstPerson['last_name'] = $secondPerson['last_name'];
                }

                if (!$secondPerson['last_name']) {
                    $secondPerson['last_name'] = $firstPerson['last_name'];
                }

                $parsedData[] = $firstPerson;
                $parsedData[] = $secondPerson;
            } else {
                //Only 1 person is present
                $parsedData[] = $this->populateDetails($people);
            }
        }

        return $parsedData;
    }

    /**
     * Remove a given text from a string
     * 
     * @param string $input
     * @param array $chars
     * @return string
     */
    public function removeTextFromString(string $input, array $texts): string
    {
        $str = $input;
        foreach($texts as $text) {
            $str = str_replace($text, '', $str);
        }
        return $str;
    }

    /**
     * For instances where two people are within the same entry, split them into individual instances based on terms which connect data
     * 
     * @param string $entry
     * @return array|string
     */
    public function splitIntoPeople(string $entry): array|string
    {
        /*
            Check if the string contains '&' or ' and ' as these are indicators that there are two or more people.

            It's important to keep the whitespace at the start and end of the 'and' search term because someone could be
            erroneously split if their name contains the term.
        */
        if (str_contains($entry, ' & ')) {
            return explode(' & ', $entry);
        }

        if (str_contains($entry, ' and ')) {
            return explode(' and ', $entry);
        }

        return $entry;
    }

    /**
     * Put each part of the persons name into an array
     * 
     * @param string $name
     * @return array
     */
    public function populateDetails(string $name): array
    {
        //Setup the array here so we can control the order easier
        $person = [
            'title' => null,
            'first_name' => null,
            'initial' => null,
            'last_name' => null
        ];

        $person['title'] = $this->getPersonsTitle($name);
        $person['initial'] = $this->getPersonsInitial($name);
        $person['last_name'] = trim($this->getPersonsSurname($name));

        //This must be after the others so we can re-use the data
        $firstName = $this->getPersonsFirstName($name, $person);
        $person['first_name'] = $firstName ? trim($firstName) : null;

        return $person;
    }

    /**
     * @param string $person
     * @return string
     */
    public function getPersonsTitle(string $person): string
    {
        //Get the first term in the string before the first instance of a whitespace
        return strtok($person, ' ');
    }
    
    /**
     * @param string $people
     * @param array $person
     * @return ?string
     */
    public function getPersonsFirstName(string $people, array $person): ?string
    {
        //Remove data we have already extracted so we are left with just their first name
        $firstName = trim($this->removeTextFromString($people, [$person['title'], ' ' . $person['initial'] . ' ', $person['last_name'], ' ']));
        return empty($firstName) ? null : $firstName;
    }

    /**
     * @param string $person
     * @return ?string
     */
    public function getPersonsInitial(string $person): ?string
    {
        //A persons initial will either be a singular character or a two characters containing a dot
        foreach(explode(' ', $person) as $part) {
            if (strlen($part) === 1 || (strlen($part) === 2 && str_contains($part, '.'))) {
                return $part;
            }
        }

        return null;
    }

    /**
     * @param string $person
     * @return string
     */
    public function getPersonsSurname(string $person): string
    {
        //Get the last bit of text after the last instance of a white space, removing the whitespace too
        return $this->removeTextFromString(strrchr($person, ' '), [' ']);
    }
}