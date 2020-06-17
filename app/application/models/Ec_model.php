<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* FruxePi (frx-dev-v0.3)
	* EC Model
	*/
	class Ec_model extends CI_Model
	{
		// Fields
		private $sensorID = 9;

		// Constructor
		public function __construct()
		{
			$this->load->database();
			$this->load->helper(array('form', 'url'));
			$this->load->library('ion_auth');
		}

		
		/**
		* Get Latest EC Reading
		* Return the latest EC status update from the database. 
		* @return void
		*/
		public function getEc()
		{
			$this->db->select("ec");
			$this->db->from("grow_data");
			$this->db->order_by("id","DESC");
			$this->db->limit(1);

			$query = $this->db->get();
			return $query->result();
		}


		/**
		* Set EC Probe GPIO Pin
		* Set the GPIO Pin associated with the EC probe.
		* @return void
		*/
		public function setGPIO()
		{
			// Set GPIO pin value and update database
			$data = array(
				"gpio_pin" => $this->input->post('GPIO') 
			);

			$this->db->where('id', $this->sensorID);
			return $this->db->update('technical', $data);
		}


		/**
		* Get EC Probe GPIO Pin
		* Return the GPIO Pin associated with the EC probe.
		* @return int
		*/
		public function getGPIO()
		{
			$this->db->select("gpio_pin");
			$this->db->from("technical");
			$this->db->where('id', $this->sensorID);

			$query = $this->db->get();
			$result = $query->result();

			return $result[0]->gpio_pin;
		}


		/**
		* Enable EC Sensor
		* Enable the EC sensor module.
		* @return void
		*/
		public function enableEcSensor()
		{
			// Set enabled field to TRUE and update database
			$data = array(
				"enabled" => TRUE 
			);

			$this->db->where('id', $this->sensorID);
			return $this->db->update('technical', $data);
		}


		/**
		* Disable EC Sensor
		* Disable the EC sensor module. 
		* @return void
		*/
		public function disableEcSensor()
		{
			// Set enabled field to FALSE and update database
			$data = array(
				"enabled" => FALSE 
			);

			$this->db->where('id', $this->sensorID);
			return $this->db->update('technical', $data);
		}

		/**
		* Get EC Probe Activation State
		* Get the activation state of the EC probe.  
		* @return boolean
		*/
		public function ecActivationState()
		{
			$this->db->select("enabled");
			$this->db->from("technical");
			$this->db->where('id', $this->sensorID);

			$query = $this->db->get();
			$result = $query->result();
			$activationState = $result[0]->enabled;

			// Return True or False based on $ec_callback value
			return $activationState;
		}


		/**
		* Read EC Sensor
		* Return an immediate reading from the EC probe.
		* @return int
		*/
		public function readEcSensor()
		{
			// GPIO pin
			$gpioPIN = $this->Ec_model->getGPIO();

			// Command string
			$command_string = "sudo /var/www/html/actions/fruxepi.py ec -er " . $gpioPIN;

			// Execute command
			exec($command_string, $ec_callback);

			return $ec_callback[0];

		}


		/**
		* EC Probe Diagnostics
		* A diagnostics function to determine the EC probe's operability.
		* @return string 
		*/
		public function ecDiagnostics()
		{
			// GPIO pin
			$gpioPIN = $this->Ec_model->getGPIO();

			// Command string
			$command_string = "sudo /var/www/html/actions/fruxepi.py ec -d " . $gpioPIN;
			
			// Execute command
			$command_callback = shell_exec($command_string);

			return $command_callback;
		}

	}

