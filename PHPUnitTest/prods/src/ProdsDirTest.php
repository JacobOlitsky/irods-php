<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProdsDirTest
 *
 * @author lisa
 */
require_once dirname(__FILE__) . '/../../../prods/src/ProdsPath.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/ProdsDir.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/ProdsFile.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/RODSAccount.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/RODSDirStats.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/RODSFileStats.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/RODSException.class.php';
require_once dirname(__FILE__) . '/../../../prods/src/RodsGenQueryNum.inc.php';
require_once dirname(__FILE__) . '/stubRODSConnManager.class.php';
require_once dirname(__FILE__) . '/stubProdsDir.class.php';

/**
 * Test class for ProdsPath.
 * Generated by PHPUnit on 2011-02-24 at 16:39:17.
 */
class ProdsDirTest extends PHPUnit_Framework_TestCase {

    /**
     * @var prodsPath
     */
    protected $prodsDir;
    protected $rodsAcct;
    protected $rodsDirStats;
    protected $path_str = "/testit/home";
    protected $parent_path;
    protected $path_exists;
    protected $name;

    // values for initialization of RODSAccount
    protected $id = "1";
    protected $user = "me";
    protected $zone = "myZone";
    protected $host = "myirods.myhost.mycom";
    protected $port = "1111";
    protected $passwd = "meme";
    protected $type = "rodsuser";
    protected $ctime = "01298479459";
    protected $mtime = "01298479459";
    protected $comment = "foo";
    protected $info = "bar";
    protected $dn = "";


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->rodsAcct = new RODSAccount($this->host, $this->port, $this->user, $this->passwd, $this->zone);
        $this->rodsDirStats = new RODSDirStats($this->user, $this->user, $this->zone, $this->mtime, $this->ctime, $this->id, $this->comment);
        $this->prodsDir = new ProdsDir($this->rodsAcct, $this->path_str);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function testFromURI() {

        // ProdsDir::fromURI does not set password, so temporarilly get rid of it for this test
        $save_pass = $this->rodsAcct->pass;
        $this->rodsAcct->pass = "";

        $uri = $this->user . "." . $this->zone . "@" . $this->host . ":" . $this->port . $this->path_str;

        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $this->path_str); // add verify and rodsDirStats
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $this->assertInstanceOf('ProdsDir', $prods_dir_stub->fromURI($uri));
        $this->assertEquals($this->prodsDir, $prods_dir_stub->fromURI($uri));
        $this->assertEquals($this->prodsDir, $prods_dir_stub->fromURI("rods://" . $uri));

        // test query and fragment in uri
        $query_and_frag = "?arg=happy#joy";
        $new_prods_dir = $prods_dir_stub->fromURI($uri . $query_and_frag);
        $expected = $this->path_str . $query_and_frag;
        $this->assertEquals($expected, $new_prods_dir->path_str);


        // reset rodsAcct password attribute
        $this->rodsAcct->pass = $save_pass;
        $uri = $this->user . "." . $this->zone . ":" . $this->passwd . "@" . $this->host . ":" . $this->port . $this->path_str;

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $this->assertEquals($this->prodsDir, $prods_dir_stub->fromURI($uri));



    }

    public function testVerify() {

        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );

        // ProdsDir::verify is a protected function - cannot mock
        // perhaps can do this with reflection (change method to public) in >= php 5.3.2

        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);

        $prods_dir_stub = $this->getMockForAbstractClass('ProdsDir', $mocked_prodsdir_construct_params);
        $meta_array = $prods_dir_stub->verify(
                array('stubRODSConnManager', 'getConn'),
                array('stubRODSConnManager', 'releaseConn'));


    }

    public function testRewind() {
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );

        // can't really test this one - no access to private variable "position"
         //$prods_dir = new stubProdsDir($this->rodsAcct, $this->path_str);
         //var_dump($prods_dir);
         //$prods_dir->rewind();
         //$this->assertEquals(0, $prods_dir->getPosition());
    }

    public function testGetNextChild() {

        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsfile_construct_params = array(&$this->rodsAcct, $path . "/filename");
        $mocked_prodschild_methods = array('exists');

        $prods_dir_child = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsdir_construct_params);
        $prods_dir_child->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $prods_file_child = $this->getMock('ProdsFile', $mocked_prodschild_methods, $mocked_prodsfile_construct_params);
        $prods_file_child->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $dirs = array($prods_dir_child, $prods_file_child);

        $mocked_prodsdir_methods = array('getAllChildren');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->once())->method('getAllChildren')->will($this->returnValue($dirs));

        $child = $prods_dir_stub->getNextChild();
        $this->assertEquals($prods_dir_child, $child);

        $child = $prods_dir_stub->getNextChild();
        $this->assertEquals($prods_file_child, $child);
    }

    public function testGetAllChildren() {
        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsfile_construct_params = array(&$this->rodsAcct, $path . "/filename");
        $mocked_prodschild_methods = array('exists');

        $prods_file_child = $this->getMock('ProdsFile', $mocked_prodschild_methods, $mocked_prodsfile_construct_params);
        $prods_file_child->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $files = array($prods_file_child);

        $prods_dir_child = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsdir_construct_params);
        $prods_dir_child->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $dirs = array($prods_dir_child);

        $mocked_prodsdir_methods = array('getChildDirs', 'getChildFiles');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->once())->method('getChildDirs')->will($this->returnValue($dirs));
        $prods_dir_stub->expects($this->once())->method('getChildFiles')->will($this->returnValue($files));

        $children = $prods_dir_stub->getAllChildren();
        $this->assertEquals($prods_file_child, $children[0]);
        $this->assertEquals($prods_dir_child, $children[1]);
    }

    public function testGetChildDirs() {

        // NEED TO ADD to this test to test parameters for sorting results
        // might need to write a stub for findDirs
        $path = "/path/tonowhere";
        $path1 = "/path/tonowhere/zeta";
        $path2 = "/path/tonowhere/alpha";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir1_construct_params = array(&$this->rodsAcct, $path1);
        $mocked_prodsdir2_construct_params = array(&$this->rodsAcct, $path2);
        $mocked_prodschild_methods = array('exists');

        $prods_dir_child1 = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsdir1_construct_params);
        $prods_dir_child1->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $prods_dir_child2 = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsdir2_construct_params);
        $prods_dir_child2->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $dirs = array($prods_dir_child1, $prods_dir_child2);

        $mocked_prodsdir_methods = array('findDirs');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->once())->method('findDirs')->will($this->returnValue($dirs));

        $children = $prods_dir_stub->getChildDirs();
        $this->assertEquals($prods_dir_child1, $children[0]);
        $this->assertEquals($prods_dir_child2, $children[1]);
    }

    public function testGetChildFiles() {

        // NEED TO ADD to this test to test parameters for sorting results
        // might need to write a stub for findFiles
        $path = "/path/tonowhere";
        $path1 = "/path/tonowhere/firstfile";
        $path2 = "/path/tonowhere/lastfile";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsfile1_construct_params = array(&$this->rodsAcct, $path1);
        $mocked_prodsfile2_construct_params = array(&$this->rodsAcct, $path2);
        $mocked_prodschild_methods = array('exists');

        $prods_file_child1 = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsfile1_construct_params);
        $prods_file_child1->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $prods_file_child2 = $this->getMock('ProdsDir', $mocked_prodschild_methods, $mocked_prodsfile2_construct_params);
        $prods_file_child2->expects($this->any())->method('exists')->will($this->returnValue(TRUE));
        $files = array($prods_file_child1, $prods_file_child2);

        $mocked_prodsdir_methods = array('findFiles');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->once())->method('findFiles')->will($this->returnValue($files));

        $children = $prods_dir_stub->getChildFiles();
        $this->assertEquals($prods_file_child1, $children[0]);
        $this->assertEquals($prods_file_child2, $children[1]);
    }

    public function testMkdir() {

        $path = "/path/tonowhere";
        $new_dir_name = "test_dir";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $new_dir = $prods_dir_stub->mkdir($new_dir_name,
                                            array('stubRODSConnManager', 'getConn'),
                                            array('stubRODSConnManager', 'releaseConn'));
        $params_array = stubRODSConn::getParams();

        // test params to RODSConn:mkdir()
        $this->assertEquals($path . "/" . $new_dir_name, $params_array[0]);

        // test newly created ProdsDir is correct
        $expectedDir = new ProdsDir($this->rodsAcct, $path . "/" . $new_dir_name);
        $this->assertEquals($expectedDir, $new_dir);
    }

    public function testRmDir() {
        $path = "/path/tonowhere";
        $rm_dir_name = "test_dir";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $empty_array = array();
        $prods_dir_stub->rmdir(TRUE, FALSE, $empty_array, NULL,
                            array('stubRODSConnManager', 'getConn'), array('stubRODSConnManager', 'releaseConn'));
        $params_array = stubRODSConn::getParams();

        // test params to RODSConn:mkdir()
        $this->assertEquals($path, $params_array[0]);
        $this->assertEquals(TRUE, $params_array[1]);
        $this->assertEquals(FALSE, $params_array[2]);
        $this->assertEquals($empty_array, $params_array[3]);
        $this->assertEquals(NULL, $params_array[4]);

    }

    public function testGetStats() {

        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $expected_stats = new RODSDirStats($path, $this->user, $this->zone, $this->mtime,
                                           $this->ctime, $this->id, $this->comment);

        $stats = $prods_dir_stub->getStats(false, array('stubRODSConnManager', 'getConn'),
                                                  array('stubRODSConnManager', 'releaseConn'));
        $this->assertEquals($expected_stats, $stats);
        // second run should exercise first if block in ProdsDir::getStats
        $stats = $prods_dir_stub->getStats(false, array('stubRODSConnManager', 'getConn'),
                                                  array('stubRODSConnManager', 'releaseConn'));
        $this->assertEquals($expected_stats, $stats);

        // run another test to force refresh of stats data
        $stats = $prods_dir_stub->getStats(true, array('stubRODSConnManager', 'getConn'),
                                                  array('stubRODSConnManager', 'releaseConn'));
        $this->assertEquals($expected_stats, $stats);
    }

    public function testQueryStatistics() {

        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $stats = $prods_dir_stub->queryStatistics("num_dirs", true, array('stubRODSConnManager', 'getConn'),
                                                  array('stubRODSConnManager', 'releaseConn'));
        $params_array = stubRODSConn::getParams();
        $actual_name = $params_array[0]->getNames();
        $this->assertEquals("COL_COLL_ID", $actual_name[0]);
        $this->assertEquals(1, $stats);

// FIX this broken test
//        $stats = $prods_dir_stub->queryStatistics("num_files", true, array('stubRODSConnManager', 'getConn'),
//                                                  array('stubRODSConnManager', 'releaseConn'));
//        $params_array = stubRODSConn::getParams();
//        $actual_name = $params_array[0]->getNames();
//        $this->assertEquals("COL_D_DATA_ID", $actual_name[0]);
//        $this->assertEquals(2, $stats);

        try {
            $stats = $prods_dir_stub->queryStatistics("something_wrong", true, array('stubRODSConnManager', 'getConn'),
                                                        array('stubRODSConnManager', 'releaseConn'));
        }
        catch(RODSException $ex) {
            return;
        }
        $this->fail("An expected exception has not been raised");

    }

    public function testFindFiles() {

        $path = "/path/tonowhere";
        $mocked_prodsdir_construct_params = array(&$this->rodsAcct, $path);
        $mocked_prodsdir_methods = array('exists');

        $prods_dir_stub = $this->getMock('ProdsDir', $mocked_prodsdir_methods, $mocked_prodsdir_construct_params);
        $prods_dir_stub->expects($this->any())->method('exists')->will($this->returnValue(TRUE));

        $terms = array("descendantOnly" => true, "recursive" => false, "logicalFile" => false);
        $total_count = -1;

        $stats = $prods_dir_stub->findFiles($terms, $total_count, 0, -1, array("name"),
                                           array('stubRODSConnManager', 'getConn'),
                                           array('stubRODSConnManager', 'releaseConn'));
        // check total count return
        $this->assertEquals(1, $total_count);

        // test limiting number of results to 0
        $prods_dir_stub->findFiles($terms, $total_count, 0, 0, array(),
                                           array('stubRODSConnManager', 'getConn'),
                                           array('stubRODSConnManager', 'releaseConn'));
        $this->assertEquals(0, $total_count);

        // test that all search conditions ($terms) are specified correctly in params to RODSConn:query()
        // 'name' (string) - partial name of the target (file or dir)
        // 'descendantOnly' (boolean) - whether to search among this directory's decendents. default is false.
        // 'recursive'      (boolean) - whether to search recursively, among all decendents and their children. default is false. This option only works when 'descendantOnly' is true
        // 'logicalFile'    (boolean) - whether to return logical file, instead of all replicas for each file. if true, the resource name for each file will be null, instead, num_replicas will be provided. default is false.
        // 'smtime'         (int)     - start last-modified-time in unix timestamp. The specified time is included in query, in other words the search can be thought was "mtime >= specified time"
        // 'emtime'         (int)     - end last-modified-time in unix timestamp. The specified time is not included in query, in other words the search can be thought was "mtime < specified time"
        // 'owner'          (string)  - owner name of the file
        // 'rescname'       (string)  - resource name of the file
        $terms = array("name" => "test");
        $prods_dir_stub->findFiles($terms, $total_count, 0, -1, array(),
                                           array('stubRODSConnManager', 'getConn'),
                                           array('stubRODSConnManager', 'releaseConn'));
        $params_array = stubRODSConn::getParams();
        $cond = $params_array[1]->getCond();


        // test all sort conditions ($sort_flds) are specified correctly in params to RODSConn:query()
        // 'name'      - name of the file or dir
        // 'size'      - size of the file
        // 'mtime'     - last modified time
        // 'ctime'     - creation time
        // 'owner'     - owner of the file
        // 'typename'  - file/data type
        // 'dirname'   - directory/collection name for the file

//        var_dump($stats);
//        $params_array = stubRODSConn::getParams();
//        var_dump($params_array);
//        $params_array = stubRODSConn::getParams();
//        $actual_name = $params_array[0]->getNames();
//        $this->assertEquals("COL_COLL_ID", $actual_name[0]);
//        $this->assertEquals(1, $stats);
    }

    public function testFindDirs() {
         // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}

//This could be used to test getChildFiles and getChildDirs

class stubFindDirs extends ProdsDir
{
  public function __construct(RODSAccount $account, $path_str, $verify=false,
    RODSDirStats $stats=NULL) {
             parent::__construct($account, $path_str, $verify, $stats);
  }

  public function findDirs(array $terms, $total_count, $start=0, $limit=-1,
    array $sort_flds=array()) {
        return $sort_flds;

  }
}

?>
