<?php

namespace App\Tests;

use App\Entity\Contract;
use App\Entity\Trainer;
use App\Service\ContractManager;
use App\Service\PlayerManager;
use App\Service\TeamManager;
use App\Service\TrainerManager;
use http\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class ApiTest extends WebTestCase
{
    public function testListPlayers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/players');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($content);
    }

    public function testListPlayer()
    {
        $client = static::createClient();
        $client->request('GET', '/api/player/17');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals("Yannick Ferreira Carrasco", $content['name']);
    }

    public function testListTeams()
    {
        $client = static::createClient();
        $client->request('GET', '/api/teams');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($content);
    }

    public function testListTeam()
    {
        $client = static::createClient();
        $client->request('GET', '/api/team/3');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Trampas Club de Futbol", $content['name']);
    }

    public function testListTrainers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/trainers');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertNotEmpty($content);
    }

    public function testListTrainer()
    {
        $client = static::createClient();
        $client->request('GET', '/api/trainer/1');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertEquals("Diego Pablo Simeone", $content['name']);
    }

    public function testListTeamPlayers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/team/players/1');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
    }

    public function testListTeamPlayersTeamNotExist()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        try {
            $client->request('GET', '/api/team/players/8');
        } catch (\Exception $e){
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
        }
    }

    public function testListTeamPlayersTeamNoPlayers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/team/players/3');
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayNotHasKey('players', $content);
    }

    public function testListSquad()
    {
        $client = static::createClient();
        $client->request('GET', '/api/team/squad/1');
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('trainer', $content);
        $this->assertArrayHasKey('players', $content);
    }

    public function testAddPlayerToTeam()
    {
        $data = array(
            'name' => 'Mario Hermoso Canseco',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Mario Hermoso Canseco", $content_player['name']);

        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    public function testAddTrainerToTeam()
    {
        $data = array(
            'name' => 'Manuel Pellegrini',
            'age' => 55
        );
        $client = static::createClient();
        $client->request('POST', '/api/trainer', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_trainer = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Manuel Pellegrini", $content_trainer['name']);

        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/trainer/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);
    }

    public function testAddPlayerToTeamAlreadyExist(){
        $data = array(
            'name' => 'Mario Hermoso Canseco',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Mario Hermoso Canseco", $content_player['name']);

        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);

        /*Again request to add-player with same player*/
        $data = array(
            'amount' => 13.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code']);
    }

    public function testAddTrainerToTeamAlreadyExist(){
        $data = array(
            'name' => 'Manuel Pellegrini',
            'age' => 55
        );
        $client = static::createClient();
        $client->request('POST', '/api/trainer', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_trainer = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Manuel Pellegrini", $content_trainer['name']);

        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/trainer/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);

        /*Again request to add-trainer with same trainer*/
        $data = array(
            'amount' => 13.5
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code']);
    }

    public function testAddPlayerToTeamActiveInOtherTeam(){
        $data = array(
            'name' => 'Mario Hermoso Canseco',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Mario Hermoso Canseco", $content_player['name']);

        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team_1 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team_1['name']);

        $data = array(
            'name' => 'Real Club Celta de Vigo S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team_2 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Club Celta de Vigo S.A.D.", $content_team_2['name']);

        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team_1['id'] . '/' . $content_player['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player['name'], $content['player']['name']);
        $this->assertEquals($content_team_1['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team_1['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);

        /*Again request to add-player, same player different team*/
        $data = array(
            'amount' => 13.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team_2['id'] . '/' . $content_player['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code']);
    }

    public function testAddTrainerToTeamActiveInOtherTeam(){
        // Create Trainer
        $data = array(
            'name' => 'Manuel Pellegrini',
            'age' => 55
        );
        $client = static::createClient();
        $client->request('POST', '/api/trainer', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_trainer = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Manuel Pellegrini", $content_trainer['name']);

        // Create Team
        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team_1 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team_1['name']);

        // Create second Team
        $data = array(
            'name' => 'Real Club Celta de Vigo S.A.D.',
            'budget' => 95
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team_2 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Club Celta de Vigo S.A.D.", $content_team_2['name']);

        // ADD trainer to Team
        $data = array(
            'amount' => 10.5
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team_1['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);
        $this->assertEquals($content_team_1['name'], $content['team']['name']);

        $client->request('GET', '/api/team/trainer/' . $content_team_1['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('trainer', $content);
        $this->assertEquals($content_trainer['name'], $content['trainer']['name']);

        /*Again request to add-trainer to other team*/
        $data = array(
            'amount' => 13.5
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team_2['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code']);
    }

    public function testAddPlayerToTeamNotEnoughMoney(){
        //create Team
        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 60
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        //create Player 1
        $data = array(
            'name' => 'Mario Hermoso Canseco',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player_1 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Mario Hermoso Canseco", $content_player_1['name']);

        //create Player 2
        $data = array(
            'name' => 'Brais Mendez Portela',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player_2 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Brais Mendez Portela", $content_player_2['name']);

        //create Player 3
        $data = array(
            'name' => 'Dennis Suarez Fernandez',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player_3 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Dennis Suarez Fernandez", $content_player_3['name']);

        //add player 1 to team
        $data = array(
            'amount' => 40.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player_1['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player_1['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player_1['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);

        //add player 2 to team
        $data = array(
            'amount' => 17.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player_2['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player_2['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player_2['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);


        //add player 3 to team, amount1 = 40.5, amount2 = 17.5, amount3 = 17.5 => more than budget
        $data = array(
            'amount' => 17.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player_3['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code'], $content['message']);
    }

    public function testAddTrainerToTeamNotEnoughMoney(){
        //create Team
        $data = array(
            'name' => 'Real Betis Balompie S.A.D.',
            'budget' => 60
        );
        $client = static::createClient();
        $client->request('POST', '/api/team', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_team = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Real Betis Balompie S.A.D.", $content_team['name']);

        //create Trainer
        $data = array(
            'name' => 'Manuel Pellegrini',
            'age' => 55
        );
        $client = static::createClient();
        $client->request('POST', '/api/trainer', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_trainer = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Manuel Pellegrini", $content_trainer['name']);

        //create Player 1
        $data = array(
            'name' => 'Mario Hermoso Canseco',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player_1 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Mario Hermoso Canseco", $content_player_1['name']);

        //create Player 2
        $data = array(
            'name' => 'Brais Mendez Portela',
            'age' => 23
        );
        $client = static::createClient();
        $client->request('POST', '/api/player', array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content_player_2 = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertEquals("Brais Mendez Portela", $content_player_2['name']);

        //add player 1 to team
        $data = array(
            'amount' => 40.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player_1['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player_1['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player_1['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);

        //add player 2 to team
        $data = array(
            'amount' => 17.5
        );
        $client->request('POST', '/api/team/add-player/' . $content_team['id'] . '/' . $content_player_2['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('team', $content);
        $this->assertArrayHasKey('player', $content);
        $this->assertEquals($content_player_2['name'], $content['player']['name']);
        $this->assertEquals($content_team['name'], $content['team']['name']);

        $client->request('GET', '/api/team/players/' . $content_team['id']);
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('players', $content);
        $this->assertNotEmpty($content['players']);
        $found = false;
        foreach ($content['players'] as $player){
            if ($player['name'] == $content_player_2['name']){
                $found = true;
            }
        }
        $this->assertTrue($found);

        //add trainer to team.. not enough money to afford
        $data = array(
            'amount' => 32
        );
        $client->request('POST', '/api/team/add-trainer/' . $content_team['id'] . '/' . $content_trainer['id'], array(), array(), array('CONTENT_TYPE' => 'application/json'), json_encode($data));
        $content = json_decode($client->getResponse()->getContent(),true);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertArrayHasKey('code', $content);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $content['code'], $content['message']);
    }
}
