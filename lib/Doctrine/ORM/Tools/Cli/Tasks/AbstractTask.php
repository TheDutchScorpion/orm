<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */
 
namespace Doctrine\ORM\Tools\Cli\Tasks;

use Doctrine\Common\Cli\Printers\AbstractPrinter,
    Doctrine\Common\Cli\TaskDocumentation,
    Doctrine\Common\Cli\OptionGroup,
    Doctrine\Common\Cli\Option;

/**
 * Base class for CLI Tasks.
 * Provides basic methods and requires implementation of methods that
 * each task should implement in order to correctly work.
 * 
 * The following arguments are common to all tasks:
 * 
 * Argument: --config=<path>
 * Description: Specifies the path to the configuration file to use. The configuration file
 *              can bootstrap an EntityManager as well as provide defaults for any cli
 *              arguments.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @version $Revision$
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
abstract class AbstractTask
{
    /**
     * @var AbstractPrinter CLI Output Printer
     */
    protected $_printer;
    
    /**
     * @var TaskDocumentation CLI Task Documentation
     */
    protected $_documentation;
    
    /**
     * @var array CLI argument options
     */
    protected $_arguments;
    
    /**
     * @var array Available CLI tasks
     */
    protected $_availableTasks;
    
    /**
     * @var EntityManager The EntityManager instance
     */
    protected $_em;
    
    /**
     * Constructor of CLI Task
     *
     * @param AbstractPrinter CLI Output Printer
     */
    public function __construct(AbstractPrinter $printer)
    {
        $this->_printer = $printer;
        $this->_documentation = new TaskDocumentation($printer);
        
        // Include configuration option
        $configGroup = new OptionGroup(OptionGroup::CARDINALITY_0_1);
        $configGroup->addOption(
            new Option('config', '<FILE_PATH>', 'Configuration file for EntityManager.')
        );
        $this->_documentation->addOption($configGroup);
        
        // Complete the CLI Task Documentation creation
        $this->buildDocumentation();
    }

    /**
     * Retrieves currently used CLI Output Printer
     *
     * @return AbstractPrinter
     */
    public function getPrinter()
    {
        return $this->_printer;
    }
    
    /**
     * Defines the CLI arguments
     *
     * @param array CLI argument options
     */
    public function setArguments($arguments)
    {
        $this->_arguments = $arguments;
    }
    
    /**
     * Retrieves current CLI arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_arguments;
    }
    
    /**
     * Defines the available CLI tasks
     *
     * @param array Available CLI tasks
     */
    public function setAvailableTasks($availableTasks)
    {
        $this->_availableTasks = $availableTasks;
    }
    
    /**
     * Retrieves the available CLI tasks
     *
     * @return array
     */
    public function getAvailableTasks()
    {
        return $this->_availableTasks;
    }
    
    /**
     * Defines the EntityManager
     *
     * @param EntityManager The EntityManager instance
     */
    public function setEntityManager($em)
    {
        $this->_em = $em;
    }
    
    /**
     * Retrieves current EntityManager
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->_em;
    }
    
    /**
     * Retrieves the CLI Task Documentation
     *
     * @return TaskDocumentation
     */
    public function getDocumentation()
    {
        return $this->_documentation;
    }
    
    /**
     * Expose to CLI Output Printer the extended help of the given task.
     * This means it should detail all parameters, options and the meaning
     * of each one.
     * This method is executed when user types in CLI the following command:
     *
     *     [bash]
     *     ./doctrine task --help
     *
     */
    public function extendedHelp()
    {
        $this->_printer->output($this->_documentation->getCompleteDocumentation());
    }
    
    /**
     * Expose to CLI Output Printer the basic help of the given task.
     * This means it should only expose the basic task call. It is also
     * executed when user calls the global help; so this means it should
     * not pollute the Printer.
     * Basic help exposure is displayed when task does not pass the validate
     * (which means when user does not type the required options or when given
     * options are invalid, ie: invalid option), or when user requests to have
     * description of all available tasks.
     * This method is executed when user uses the following commands:
     *
     *     [bash]
     *     ./doctrine task --invalid-option
     *     ./doctrine --help
     *
     */
    public function basicHelp()
    {
        $this->_printer
             ->output($this->_documentation->getSynopsis())
             ->output(PHP_EOL)
             ->output('  ' . $this->_documentation->getDescription())
             ->output(PHP_EOL . PHP_EOL);
    }
    
    /**
     * Assures the given arguments matches with required/optional ones.
     * This method should be used to introspect arguments to check for
     * missing required arguments and also for invalid defined options.
     *
     * @return boolean
     */
    abstract public function validate();
    
    /**
     * Safely execution of task.
     * Each CLI task should implement this as normal flow execution of
     * what is supposed to do.
     */
    abstract public function run();
    
    /**
     * Generate the CLI Task Documentation
     */
    abstract public function buildDocumentation();
}