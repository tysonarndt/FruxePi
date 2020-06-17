<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	/**
	* FruxePi (frx-dev-v0.3)
	* Ph Model
	*/
	class Ph_model extends CI_Model
	{
		// Fields
		private $sensorID = 8;

		// Constructor
		public function __construct()
		{
			$this->load->database();
            $this->load->helper(array('form', 'url'));
            $this->load->library('ion_auth');
		}

		
		/**
		* Get Latest pH Reading
		* Return the latest pH status update from the database. 
		* @return void
		*/
		public function getpH()
		{
			$this->db->select("ph");
			$this->db->from("grow_data");
			$this->db->order_by("id","DESC");
			$this->db->limit(1);

			$query = $this->db->get();
			return $query->result();
		}


		/**
		* Set pH Probe GPIO Pin
		* Set the GPIO Pin associated with the pH probe.
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
		* Get pH Probe GPIO Pin
		* Return the GPIO Pin associated with the pH probe.
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
		* Enable pH Sensor
		* Enable the pH sensor module.
		* @return void
		*/
		public function enablePhSensor()
		{
			// Set enabled field to TRUE and update database
			$data = array(
				"enabled" => TRUE 
			);

			$this->db->where('id', $this->sensorID);
			return $this->db->update('technical', $data);
		}


		/**
		* Disable pH Sensor
		* Disable the pH sensor module. 
		* @return void
		*/
		public function disablePhSensor()
		{
			// Set enabled field to FALSE and update database
			$data = array(
				"enabled" => FALSE 
			);

			$this->db->where('id', $this->sensorID);
			return $this->db->update('technical', $data);
		}

		/**
		* Get pH Probe Activation State
		* Get the activation state of the pH probe.  
		* @return boolean
		*/
		public function phActivationState()
		{
			$this->db->select("enabled");
			$this->db->from("technical");
			$this->db->where('id', $this->sensorID);

			$query = $this->db->get();
			$result = $query->result();
			$activationState = $result[0]->enabled;

			// Return True or False based on $ph_callback value
			return $activationState;
		}


		/**
		* Read pH Sensor
		* Return an immediate reading from the pH probe.
		* @return int
		*/
		public function readPhSensor()
		{
			// GPIO pin
			$gpioPIN = $this->Ph_model->getGPIO();

			// Command string
			$command_string = "sudo /var/www/html/actions/fruxepi.py ph -pr " . $gpioPIN;

			// Execute command
			exec($command_string, $ph_callback);

			return $ph_callback[0];

		}


		/**
		* pH Probe Diagnostics
		* A diagnostics function to determine the pH probe's operability.
		* @return string 
		*/
		public function phDiagnostics()
		{
			// GPIO pin
			$gpioPIN = $this->Ph_model->getGPIO();

			// Command string
			$command_string = "sudo /var/www/html/actions/fruxepi.py ph -d " . $gpioPIN;
			
			// Execute command
			$command_callback = shell_exec($command_string);

			return $command_callback;
		}

	}

