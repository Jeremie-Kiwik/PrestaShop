<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDomainException;

/**
 * Contract for handling DeleteExtraPropertyDefinitionCommand.
 *
 * Implementations remove the registry row and, when requested, drop the physical
 * SQL column from the entity's extra table.
 */
interface DeleteExtraPropertyDefinitionCommandHandlerInterface
{
    /**
     * @param DeleteExtraPropertyDefinitionCommand $command
     *
     * @throws ExtraPropertyDomainException
     */
    public function handle(DeleteExtraPropertyDefinitionCommand $command): void;
}
