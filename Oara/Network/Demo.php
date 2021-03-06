<?php
/**
 * Data Class
 *
 * @author     Carlos Morillo Merino
 * @category   Oara_Network_Demo
 * @copyright  Fubra Limited
 * @version    Release: 01.00
 *
 */
class Oara_Network_Demo extends Oara_Network{

	private $_affiliateNetwork = null;

	private $_merchantList = array("Acme Corp" , "Allied Biscuit" , "Ankh-Sto Associates" , "Extensive Enterprise" , "Corp" , "Globo-Chem",
								   "Mr. Sparkle", "Globex Corporation", "LexCorp", "LuthorCorp", "North Central Electronics",
								   "Omni Consumer Products", "Praxis Corporation", "Sombra Corporation" , "Sto Plains Holdings",
								   "Sto Plains Holdings", "Yuhu Limited");

	private $_linkList = array("homepage_content", "shopping_cart_sidenav", "footer", "linkspage", "homepage_header", "para_one_content_home");

	private $_websiteList = array("Money Guide Site", "Football Guide Site", "Hotel Guide Site", "Browser Guide Site", "Paper Cup Site",
								  "Lightbulb Shop Site");

	private $_pageList = array("/home.html", "/sales.html", "/contact.html", "/book.html", "/index.html","/info.html");

	/**
	 * Constructor and Login
	 * @param $cartrawler
	 * @return Oara_Network_Demo_Export
	 */
	public function __construct($credentials)
	{

	}
	/**
	 * Check the connection
	 */
	public function checkConnection(){
		$connection = true;
		return $connection;
	}
	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Base#getMerchantList()
	 */
	public function getMerchantList($merchantMap = array())
	{
		$merchants = Array();
		$merchantsNumber = count($this->_merchantList);

		for($i = 0; $i < $merchantsNumber ; $i++){
			//Getting the array Id
			$obj = Array();
			$obj['cid'] = $i;
			$obj['name'] = $this->_merchantList[$i];
			$merchants[] = $obj;
		}
		return $merchants;
	}
	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Base#getTransactionList($merchantId, $dStartDate, $dEndDate)
	 */
	public function getTransactionList($merchantList = null , Zend_Date $dStartDate = null , Zend_Date $dEndDate = null)
	{
		$totalTransactions = Array();
		$transactionNumber = rand(1, 200);
		$twoMonthsAgoDate = new Zend_Date();
		$twoMonthsAgoDate->subMonth(2);
		$dateArray = Oara_Utilities::daysOfDifference($dStartDate, $dEndDate);
		for ($i = 0; $i < $transactionNumber; $i++){
			$dateIndex = rand(0, count($dateArray)-1);
			$merchantIndex = rand(0, count($merchantList)-1);
			$transaction = array();
			$transaction['merchantId'] = $merchantList[$merchantIndex];
			$transaction['date'] = $dateArray[$dateIndex]->toString("yyyy-MM-dd HH:mm:ss");
			$transactionAmount = rand(1, 1000);
			$transaction['amount'] = $transactionAmount;
			$transaction['commission'] = $transactionAmount/10;
			$transaction['link'] = $this->_linkList[rand(0, count($this->_linkList)-1)];
			$transaction['website'] = $this->_websiteList[rand(0, count($this->_websiteList)-1)];
			$transaction['page'] = $this->_pageList[rand(0, count($this->_pageList)-1)];
			$transactionStatusChances = rand(1, 100);
			if ($dateArray[$dateIndex]->compare($twoMonthsAgoDate) >= 0){
				if ($transactionStatusChances < 60){
					$transaction['status'] = Oara_Utilities::STATUS_CONFIRMED;
				} else if ($transactionStatusChances < 70){
					$transaction['status'] = Oara_Utilities::STATUS_DECLINED;
				} else {
					$transaction['status'] = Oara_Utilities::STATUS_PENDING;
				}
			} else {
				if ($transactionStatusChances < 80){
					$transaction['status'] = Oara_Utilities::STATUS_CONFIRMED;
				} else {
					$transaction['status'] = Oara_Utilities::STATUS_DECLINED;
				}
			}
			$totalTransactions[] = $transaction;
		}
		return $totalTransactions;

	}

	/**
	 * (non-PHPdoc)
	 * @see library/Oara/Network/Oara_Network_Base#getOverviewList($merchantId, $dStartDate, $dEndDate)
	 */
	public function getOverviewList($transactionList = null, $merchantList = null, Zend_Date $dStartDate = null, Zend_Date $dEndDate = null){
		$totalOverviews = Array();
		$transactionArray = self::transactionMapPerDay($transactionList);
		foreach ($transactionArray as $merchantId => $linkList){
			foreach ($linkList as $link => $websiteList){
				foreach ($websiteList as $website => $pageList){
					foreach ($pageList as $page => $dateList){
						foreach ($dateList as $date => $transactionList){
								
							$overview = Array();

							$overview['merchantId'] = $merchantId;
							$overviewDate = new Zend_Date($date, "yyyy-MM-dd");
							$overview['date'] = $overviewDate->toString("yyyy-MM-dd HH:mm:ss");
							$clickNumber = rand(1, 30);
							$overview['click_number'] = $clickNumber;
							$overview['link'] = $link;
							$overview['website'] = $website;
							$overview['page'] = $page;
							$impressionNumber = rand(1, 60);
							$overview['impression_number'] = $impressionNumber;
							$overview['transaction_number'] = 0;
							$overview['transaction_confirmed_value'] = 0;
							$overview['transaction_confirmed_commission']= 0;
							$overview['transaction_pending_value']= 0;
							$overview['transaction_pending_commission']= 0;
							$overview['transaction_declined_value']= 0;
							$overview['transaction_declined_commission']= 0;
							foreach ($transactionList as $transaction){
								$overview['transaction_number'] ++;
								if ($transaction['status'] == Oara_Utilities::STATUS_CONFIRMED){
									$overview['transaction_confirmed_value'] += $transaction['amount'];
									$overview['transaction_confirmed_commission'] += $transaction['commission'];
								} else if ($transaction['status'] == Oara_Utilities::STATUS_PENDING){
									$overview['transaction_pending_value'] += $transaction['amount'];
									$overview['transaction_pending_commission'] += $transaction['commission'];
								} else if ($transaction['status'] == Oara_Utilities::STATUS_DECLINED){
									$overview['transaction_declined_value'] += $transaction['amount'];
									$overview['transaction_declined_commission'] += $transaction['commission'];
								}
							}
							$totalOverviews[] = $overview;
						}
					}
				}
			}
		}
		$dateArray = Oara_Utilities::daysOfDifference($dStartDate, $dEndDate);
		$overviewNumber = rand(1, 20);
		for ($i = 0; $i < $overviewNumber; $i++){
			$dateIndex = rand(0, count($dateArray)-1);
			$merchantIndex = rand(0, count($merchantList)-1);
			$overview = Array();
			$overview['merchantId'] = $merchantList[$merchantIndex];
			$overview['date'] = $dateArray[$dateIndex]->toString("yyyy-MM-dd HH:mm:ss");
			$clickNumber = rand(1, 30);
			$overview['click_number'] = $clickNumber;
			$overview['link'] = $this->_linkList[rand(0, count($this->_linkList)-1)];
			$overview['website'] = $this->_websiteList[rand(0, count($this->_websiteList)-1)];
			$overview['page'] = $this->_pageList[rand(0, count($this->_pageList)-1)];
			$impressionNumber = rand(1, 60);
			$overview['impression_number'] = $impressionNumber;
			$overview['transaction_number'] = 0;
			$overview['transaction_confirmed_value'] = 0;
			$overview['transaction_confirmed_commission']= 0;
			$overview['transaction_pending_value']= 0;
			$overview['transaction_pending_commission']= 0;
			$overview['transaction_declined_value']= 0;
			$overview['transaction_declined_commission']= 0;
			$totalOverviews[] = $overview;
		}
		return $totalOverviews;
	}

	/**
	 * (non-PHPdoc)
	 * @see Oara/Network/Oara_Network_Base#getPaymentHistory()
	 */
	public function getPaymentHistory(){
		$paymentHistory = array();
		$startDate = new Zend_Date('01-01-2009', 'dd-MM-yyyy');
		$endDate = new Zend_Date();
		$dateArray = Oara_Utilities::monthsOfDifference($startDate, $endDate);
		for ($i = 0; $i < count($dateArray); $i++){
			$dateMonth = $dateArray[$i];
			$obj = array();
			$obj['date'] = $dateMonth->toString("yyyy-MM-dd HH:mm:ss");
			$value = rand(1,1300);
			$obj['value'] = $value;
			$obj['method'] = 'BACS';
			$obj['pid'] = $dateMonth->toString('yyyyMMdd');
			$paymentHistory[] = $obj;
		}
		return $paymentHistory;
	}

	/**
	 * Filter the transactionList per day
	 * @param array $transactionList
	 * @return array
	 */
	public function transactionMapPerDay(array $transactionList){
		$transactionMap = array();
		foreach ($transactionList as $transaction){
			$dateString = substr($transaction['date'], 0, 10);
			if (!isset($transactionMap[$transaction['merchantId']][$transaction['link']][$transaction['website']][$transaction['page']][$dateString])){
				$transactionMap[$transaction['merchantId']][$transaction['link']][$transaction['website']][$transaction['page']][$dateString] = array();
			}

			$transactionMap[$transaction['merchantId']][$transaction['link']][$transaction['website']][$transaction['page']][$dateString][] = $transaction;
		}
		return $transactionMap;
	}

}