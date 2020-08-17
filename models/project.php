<?php

/**
 * Project Model
 * Contains information on the projects
 *
 * PHP version 7+
 *
 * @category   Models
 * @package    Ram
 * @author     Jeroen Carpentier <jeroen@vansteinengroentjes.nl>
 * @author     Tom Groentjes <tom@vansteinengroentjes.nl>
 * @author     Bas van Stein <bas@vansteinengroentjes.nl>
 * @copyright  2020 Van Stein en Groentjes B.V.
 * @license    GNU Public License V3 or later (GPL-3.0-or-later)
 * @version    GIT: $Id$
 * @link       </TODO>: set Git Link
 * @uses       \SG\Ram\Model                    Extend the main Model.
 * @uses       \SG\Ram\Models\Person            Person object
 * @uses       DateTime                         PHP DateTime object
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details. <https://www.gnu.org/licenses/>
 *
 */

namespace SG\Ram\Models;

use SG\Ram\Model;
use DateTime;
use SG\Ram\Models\Person;

/**
 * Project model
 * @category   Models
 * @package    SG
 */
class Project extends Model
{
    public $name;

    private $_id;
    private $_developmentLink;
    private $_image;
    private $_description;
    private $_projectStatus;
    private $_lastChanged;
    private $_lastChangedDb;
    private $_created;
    private $_createdDb;
    private $_companyId;
    private $_customerMainContactId;
    private $_contractorId;
    private $_intermediateId;
    private $_contractorMainContactId;
    private $_androidLink;
    private $_iosLink;
    private $_webLink;
    private $_budget;
    private $_hourlyWage;
    private $_SOHourly;
    private $_SODescription;
    private $_deadline;
    private $_deadLinkDb;
    private $_repository;
    private $_statusDisplay;
    private $_statusText;
    private $_teamSize = 3;
    private $_attachmentSize = 4;
    private $_ticketSize = 12;
    private $_slug;
    private $_responsible;
    private $_nameMainContact;
    private $_nameCompany;
    private $_contracterName;
    private $_intermediateName;
    private $_contractorMainContactName;
    private $_responsibleName;
    private $_AllMainContact;
    private $_AllCompany;
    private $_contracterAll;
    private $_intermediateAll;
    private $_contractorMainContactAll;
    private $_responsibleAll;
    
    /**
     * Constructor.
     * @param Array | null $data Data to fill the object.
     * @return Void.
     */
    public function __construct($data = null) {
        parent::__construct();
        if ($data) {
            $this->buildObject($data);
        }
    }

    /**
     * Destructor.
     * @return Void.
     */
    public function __destruct() {
        parent::__destruct();
    }
    
    /**
     * Builds object is data comes from database.
     * @param Array $data Row of the database to fill the model
     * @return  Void
     */
    private function buildObject($data) {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setImage($data['image']);
        $this->setAndroidLink($data['android_link']);
        $this->setIosLink($data['ios_link']);
        $this->setWebLink($data['web_link']);
        $this->setDevLink($data['dev_link']);
        $this->setRepository($data['repository']);
        $this->setSlug($data['slug']);
        $this->setDeadline($data['deadline']);
        $this->setCompanyId($data['company_id']);
        $this->setLastchanged($data['last_changed']);
        $this->setCustomerMainContactId($data['person_main_contact_id']);
        $this->setContractorId($data['contractor_id']);
        $this->setContractorMainContactId($data['contractor_main_contact_id']);
        $this->setIntermediateId($data['intermediate_id']);
        $this->setResponsible($data['responsible']);
        $this->setProjectStatus($data['project_status']);
        $this->setBudget($data['budget']);
        $this->setHourlywage($data['hourly_wage']);
        $this->setSOHourly($data["SO_hourly"]);
        $this->setSODescription($data["SO_description"]);
        $this->setDescription($data['description']);
        $this->setOtherVariables($data);
    }
    
    /**
     * Builds object is data comes from database.
     * @param Array $data Row of the database to fill the Set functions.
     * @return  Void.
     */
    public function setOtherVariables($data) {
        foreach ($data as $key => $single) {
            if (strpos($key, '_name') || strpos($key, '_all')) {
                switch ($key) {
                    case "person_main_contact_name":
                        $this->setNameMainContact($single);
                        break;
                    case "company_name":
                        $this->setNameCompany($single);
                        break;
                    case "contractor_name":
                        $this->setContractorName($single);
                        break;
                    case "intermediate_name":
                        $this->setIntermediateName($single);
                        break;
                    case "contractor_main_contact_name":
                        $this->setContractorMainContactName($single);
                        break;
                    case "responsible_name":
                        $this->setResponsibleName($single);
                        break;
                    case "person_main_contact_all":
                        $this->setAllMainContact($single);
                        break;
                    case "company_all":
                        $this->setAllCompany($single);
                        break;
                    case "contractor_all":
                        $this->setContractorAll($single);
                        break;
                    case "intermediate_all":
                        $this->setIntermediateAll($single);
                        break;
                    case "contractor_main_contact_all":
                        $this->setContractorMainContactAll($single);
                        break;
                    case "responsible_all":
                        $this->setResponsibleAll($single);
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
    /**
     * Getters
     */
    /**
     * Get NameMainContact.
     * @return String NameMainContact.
     */
    public function getNameMainContact() {
        return $this->_nameMainContact;
    }
    
    /**
     * Get NameCompany.
     * @return String NameCompany.
     */
    public function getNameCompany() {
        return $this->_nameCompany;
    }
    
    /**
     * Get ContractorName.
     * @return String ContractorName.
     */
    public function getContractorName() {
        return $this->_contracterName;
    }
    
    /**
     * Get IntermediateName.
     * @return String IntermediateName.
     */
    public function getIntermediateName() {
        return $this->_intermediateName;
    }
    
    /**
     * Get ContractorMainContactName.
     * @return String ContractorMainContactName.
     */
    public function getContractorMainContactName() {
        return $this->_contractorMainContactName;
    }
    
    /**
     * Get CustomerMainContactId.
     * @return Integer CustomerMainContactId.
     */
    public function getCustomerMainContactId() {
        return $this->_customerMainContactId;
    }
    
    /**
     * Get ResponsibleName.
     * @return String ResponsibleName.
     */
    public function getResponsibleName() {
        return $this->_responsibleName;
    }
    
    /**
     * Get AllMainContact.
     * @return String AllMainContact.
     */
    public function getAllMainContact() {
        if (is_a($this->_AllMainContact, 'SG\Ram\Models\Person')) {
            return $this->_AllMainContact;
        } else {
            return new Person();
        }
    }
    
    /**
     * Get AllCompany.
     * @return String AllCompany.
     */
    public function getAllCompany() {
        if (is_a($this->_AllCompany, 'SG\Ram\Models\Company')) {
            return $this->_AllCompany;
        } else {
            return new Company();
        }
    }
    
    /**
     * Get ContractorAll.
     * @return String ContractorAll.
     */
    public function getContractorAll() {
        if (is_a($this->_contracterAll, 'SG\Ram\Models\Company')) {
            return $this->_contracterAll;
        } else {
            return new Company();
        }
    }
    
    /**
     * Get IntermediateAll.
     * @return String IntermediateAll.
     */
    public function getIntermediateAll() {
        if (is_a($this->_intermediateAll, 'SG\Ram\Models\Company')) {
            return $this->_intermediateAll;
        } else {
            return new Company();
        }
    }
    
    /**
     * Get ContractorMainContactAll.
     * @return String ContractorMainContactAll.
     */
    public function getContractorMainContactAll() {
        if (is_a($this->_contractorMainContactAll, 'SG\Ram\Models\Person')) {
            return $this->_contractorMainContactAll;
        } else {
            return new Person();
        }
    }
    
    /**
     * Get ResponsibleAll.
     * @return String ResponsibleAll.
     */
    public function getResponsibleAll() {
        if (is_a($this->_responsibleAll, 'SG\Ram\Models\Person')) {
            return $this->_responsibleAll;
        } else {
            return new Person();
        }
    }

    /**
     * Get Id.
     * @return Integer Id.
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Get name.
     * @return String Name.
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get dev_link.
     * @return String   Development link
     */
    public function getDevLink() {
        return !empty($this->_developmentLink) ? $this->createLink($this->_developmentLink) : "";
    }

    /**
     * Get Image.
     * @return String Image.
     */
    public function getImage() {
        $image = file_exists("./img/projects/" . $this->_image);
        if (strlen($this->_image) < 5 || !$image) {
            $this->_image = "no_avatar.jpg";
        }
        return $this->_image;
    }

    /**
     * Get Description.
     * @return String Description.
     */
    public function getDescription() {
        return $this->_description;
    }

    /**
     * Get ProjectStatus.
     * @return String ProjectStatus.
     */
    public function getProjectStatus() {
        return $this->_projectStatus;
    }

    /**
     * Get LastChanged.
     * @return String LastChanged.
     */
    public function getLastChanged() {
        return $this->_lastChanged;
    }
    
    /**
     * Get LastchangedDmy.
     * @return date $temp | null.
     */
    public function getLastchangedDmy() {
        return $this->_lastChanged ? (new DateTime($this->_lastChanged))->format('d-m-Y') : "";
    }

    /**
     * Get LastchangedDb.
     * @return String LastchangedDb.
     */
    public function getLastchangedDb() {
        return $this->_lastChangedDb;
    }

    /**
     * Get Created.
     * @return String Created.
     */
    public function getCreated() {
        return $this->_created;
    }

    /**
     * Get CreatedDb.
     * @return String CreatedDb.
     */
    public function getCreatedDb() {
        return $this->_createdDb;
    }

    /**
     * Get CompanyId.
     * @return String CompanyId.
     */
    public function getCompanyId() {
        return $this->_companyId;
    }

    /**
     * Get ContractorId.
     * @return Integer ContractorId.
     */
    public function getContractorId() {
        return $this->_contractorId;
    }

    /**
     * Get IntermediateId.
     * @return Integer IntermediateId.
     */
    public function getIntermediateId() {
        return $this->_intermediateId;
    }

    /**
     * Get ContractorMainContactId.
     * @return Integer ContractorMainContactId.
     */
    public function getContractorMainContactId() {
        return $this->_contractorMainContactId;
    }

    /**
     * Get AndroidLink.
     * @return String AndroidLink.
     */
    public function getAndroidLink() {
        return !empty($this->_androidLink) ? $this->createLink($this->_androidLink) : "";
    }

    /**
     * Get IosLink.
     * @return String IosLink.
     */
    public function getIosLink() {
        return !empty($this->_iosLink) ? $this->createLink($this->_iosLink) : "";
    }

    /**
     * Get WebLink.
     * @return String WebLink.
     */
    public function getWebLink() {
        return !empty($this->_webLink) ? $this->createLink($this->_webLink) : "";
    }

    /**
     * Get Budget.
     * @return String Budget.
     */
    public function getBudget() {
        return $this->_budget;
    }
    
    /**
     * Get HourlyWage.
     * @return String HourlyWage.
     */
    public function getHourlyWage() {
        return $this->_hourlyWage;
    }
    
    /**
     * Get SOHourly.
     * @return String SOHourly.
     */
    public function getSOHourly() {
        return $this->_SOHourly;
    }
    
    /**
     * Get SODescription.
     * @return String SODescription.
     */
    public function getSODescription() {
        return $this->_SODescription;
    }

    /**
     * Get Deadline.
     * @return String Deadline.
     */
    public function getDeadline() {
        return $this->_deadline;
    }
    
    /**
     * Get DeadlineDmy.
     * @return date $temp | null.
     */
    public function getDeadlineDmy() {
        return $this->_deadline ? (new DateTime($this->_deadline))->format('d-m-Y') : "";
    }

    /**
     * Get DeadlinkDb.
     * @return String DeadlinkDb.
     */
    public function getDeadlinkDb() {
        return $this->_deadLinkDb;
    }

    /**
     * Get Repository.
     * @return String Repository.
     */
    public function getRepository() {
        return !empty($this->_repository) ? $this->createLink($this->_repository) : "";
    }

    /**
     * Get StatusDisplay.
     * @return String StatusDisplay.
     */
    public function getStatusDisplay() {
        return $this->_statusDisplay;
    }

    /**
     * Get StatusText.
     * @return String StatusText.
     */
    public function getStatusText() {
        return $this->_statusText;
    }

    /**
     * Get TeamSize.
     * @return Integer TeamSize.
     */
    public function getTeamSize() {
        return $this->_teamSize;
    }

    /**
     * Get AttachmentSize.
     * @return Integer AttachmentSize.
     */
    public function getAttachmentSize() {
        return $this->_attachmentSize;
    }

    /**
     * Get TicketSize.
     * @return Integer TicketSize.
     */
    public function getTicketSize() {
        return $this->_ticketSize;
    }

    /**
     * Get Slug.
     * @return Integer Slug.
     */
    public function getSlug() {
        return $this->_slug;
    }

    /**
     * Get Responsible.
     * @return Integer Responsible.
     */
    public function getResponsible() {
        return $this->_responsible;
    }

    /**
     * Setters.
     */
    /**
     * Set the id.
     * @param Integer $id id.
     * @return  Void.
     */
    public function setId($id) {
        $this->_id = $id;
    }

    /**
     * Set the name.
     * @param String $name name.
     * @return  Void.
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Set the DevLink.
     * @param String $dev_link DevLink.
     * @return  Void.
     */
    public function setDevLink($dev_link) {
        $this->_developmentLink = $dev_link;
    }

    /**
     * Set the image.
     * @param String $image image.
     * @return  Void.
     */
    public function setImage($image) {
        $this->_image = $image;
    }

    /**
     * Set the description.
     * @param String $description description.
     * @return  Void.
     */
    public function setDescription($description) {
        $this->_description = $description;
    }

    /**
     * Set the project_status.
     * @param String $project_status project_status.
     * @return  Void.
     */
    public function setProjectStatus($project_status) {
        $this->_projectStatus = $project_status;
    }

    /**
     * Set the Lastchanged.
     * @param String $lastchanged Lastchanged.
     * @return  Void.
     */
    public function setLastchanged($lastchanged) {
        $this->_lastChanged = $lastchanged;
    }

    /**
     * Set the LastchangedDb.
     * @param String $lastchanged_db LastchangedDb.
     * @return  Void.
     */
    public function setLastchangedDb($lastchanged_db) {
        $this->_lastChangedDb = $lastchanged_db;
    }

    /**
     * Set the created.
     * @param String $created created.
     * @return  Void.
     */
    public function setCreated($created) {
        $this->_created = $created;
    }

    /**
     * Set the CreatedDb.
     * @param String $created_db CreatedDb.
     * @return  Void.
     */
    public function setCreatedDb($created_db) {
        $this->_createdDb = $created_db;
    }

    /**
     * Set the CompanyId.
     * @param Integer $company_id CompanyId.
     * @return  Void.
     */
    public function setCompanyId($company_id) {
        $this->_companyId = $company_id;
    }

    /**
     * Set the ContractorId.
     * @param Integer $contractor_id ContractorId.
     * @return  Void.
     */
    public function setContractorId($contractor_id) {
        $this->_contractorId = $contractor_id;
    }

    /**
     * Set the IntermediateId.
     * @param Integer $intermediate_id IntermediateId.
     * @return  Void.
     */
    public function setIntermediateId($intermediate_id) {
        $this->_intermediateId = $intermediate_id;
    }

    /**
     * Set the $contractor_main_contact_id.
     * @param Integer $contractor_main_contact_id ContractorMainContactId.
     * @return  Void.
     */
    public function setContractorMainContactId($contractor_main_contact_id) {
        $this->_contractorMainContactId = $contractor_main_contact_id;
    }

    /**
     * Set the AndroidLink.
     * @param String $android_link AndroidLink.
     * @return  Void.
     */
    public function setAndroidLink($android_link) {
        $this->_androidLink = $android_link;
    }

    /**
     * Set the IosLink.
     * @param String $ios_link IosLink.
     * @return  Void.
     */
    public function setIosLink($ios_link) {
        $this->_iosLink = $ios_link;
    }

    /**
     * Set the WebLink.
     * @param String $web_link WebLink.
     * @return  Void.
     */
    public function setWebLink($web_link) {
        $this->_webLink = $web_link;
    }

    /**
     * Set the Budget.
     * @param String $budget Budget.
     * @return  Void.
     */
    public function setBudget($budget) {
        $this->_budget = $budget;
    }
    
    /**
     * Set the HourlyWage.
     * @param String $hourly_wage HourlyWage.
     * @return  Void.
     */
    public function setHourlyWage($hourly_wage) {
        $this->_hourlyWage = $hourly_wage;
    }
    
    /**
     * Set the SOHourly.
     * @param String $SOHourly SOHourly.
     * @return  Void.
     */
    public function setSOHourly($SOHourly) {
        $this->_SOHourly = $SOHourly;
    }
    
    /**
     * Set the SODescription.
     * @param String $SODesctiption HourlyWage.
     * @return  Void.
     */
    public function setSODescription($SODesctiption) {
        $this->_SODescription = $SODesctiption;
    }

    /**
     * Set the Deadline.
     * @param String $deadline Deadline.
     * @return  Void.
     */
    public function setDeadline($deadline) {
        $this->_deadline = $deadline;
    }

    /**
     * Set the DeadlinkDb.
     * @param String $deadlink_db DeadlinkDb.
     * @return  Void.
     */
    public function setDeadlinkDb($deadlink_db) {
        $this->_deadLinkDb = $deadlink_db;
    }

    /**
     * Set the Repository.
     * @param String $repository Repository.
     * @return  Void.
     */
    public function setRepository($repository) {
        $this->_repository = $repository;
    }

    /**
     * Set the StatusDisplay.
     * @param String $status_display StatusDisplay.
     * @return  Void.
     */
    public function setStatusDisplay($status_display) {
        $this->_statusDisplay = $status_display;
    }

    /**
     * Set the StatusText.
     * @param String $status_text StatusText.
     * @return  Void.
     */
    public function setStatusText($status_text) {
        $this->_statusText = $status_text;
    }

    /**
     * Set the TeamSize.
     * @param Integer $team_size TeamSize.
     * @return  Void.
     */
    public function setTeamSize($team_size) {
        $this->_teamSize = $team_size;
    }

    /**
     * Set the AttachmentSize.
     * @param Integer $attachment_size AttachmentSize.
     * @return  Void.
     */
    public function setAttachmentSize($attachment_size) {
        $this->_attachmentSize = $attachment_size;
    }

     /**
     * Set the TicketSize.
     * @param Integer $ticket_size TicketSize.
     * @return  Void.
     */
    public function setTicketSize($ticket_size) {
        $this->_ticketSize = $ticket_size;
    }

    /**
     * Set the Slug.
     * @param String $slug Slug.
     * @return  Void.
     */
    public function setSlug($slug) {
        $this->_slug = $slug;
    }

    /**
     * Set the Responsible.
     * @param String $responsible Responsible.
     * @return  Void.
     */
    public function setResponsible($responsible) {
        $this->_responsible = $responsible;
    }

    /**
     * Set the CustomerMainContactId.
     * @param String $customer_main_contact_id CustomerMainContactId.
     * @return  Void.
     */
    public function setCustomerMainContactId($customer_main_contact_id) {
        $this->_customerMainContactId = $customer_main_contact_id;
    }
    
    /**
     * Set the NameMainContact.
     * @param String $name NameMainContact.
     * @return  Void.
     */
    public function setNameMainContact($name) {
        $this->_nameMainContact = $name;
    }
    
    /**
     * Set the NameCompany.
     * @param String $name NameCompany.
     * @return  Void.
     */
    public function setNameCompany($name) {
        $this->_nameCompany = $name;
    }
    
    /**
     * Set the ContractorName.
     * @param String $name ContractorName.
     * @return  Void.
     */
    public function setContractorName($name) {
        $this->_contracterName = $name;
    }
    
    /**
     * Set the IntermediateName.
     * @param String $name IntermediateName.
     * @return  Void.
     */
    public function setIntermediateName($name) {
        $this->_intermediateName = $name;
    }
    
    /**
     * Set the ContractorMainContactName.
     * @param String $name ContractorMainContactName.
     * @return  Void.
     */
    public function setContractorMainContactName($name) {
        $this->_contractorMainContactName = $name;
    }
    
    /**
     * Set the ResponsibleName.
     * @param String $name ResponsibleName.
     * @return  Void.
     */
    public function setResponsibleName($name) {
        $this->_responsibleName = $name;
    }
    
    /**
     * Set the AllMainContact.
     * @param String $name AllMainContact.
     * @return  Void.
     */
    public function setAllMainContact($name) {
        $this->_AllMainContact = $name;
    }
    
    /**
     * Set the AllCompany.
     * @param String $name AllCompany.
     * @return  Void.
     */
    public function setAllCompany($name) {
        $this->_AllCompany = $name;
    }
    
    /**
     * Set the ContractorAll.
     * @param String $name ContractorAll.
     * @return  Void.
     */
    public function setContractorAll($name) {
        $this->_contracterAll = $name;
    }
    
    /**
     * Set the IntermediateAll.
     * @param String $name IntermediateAll.
     * @return  Void.
     */
    public function setIntermediateAll($name) {
        $this->_intermediateAll = $name;
    }
    
    /**
     * Set the ContractorMainContactAll.
     * @param String $name ContractorMainContactAll.
     * @return  Void.
     */
    public function setContractorMainContactAll($name) {
        $this->_contractorMainContactAll = $name;
    }
    
    /**
     * Set the ResponsibleAll.
     * @param String $name ResponsibleAll.
     * @return  Void.
     */
    public function setResponsibleAll($name) {
        $this->_responsibleAll = is_array($name) ? new Person($name) : $name;
    }
}
